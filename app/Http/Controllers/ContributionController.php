<?php

namespace App\Http\Controllers;

use App\Jobs\StampReceiptPdf;
use App\Models\Contribution;
use App\Models\Daret;
use App\Models\DaretCycle;
use App\Notifications\ReceiptStatusChanged;
use App\Notifications\ReceiptUploaded;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContributionController extends Controller
{
    public function showCycle(Request $request, Daret $daret, DaretCycle $cycle): View
    {
        $user = $request->user();

        if ($cycle->daret_id !== $daret->id) {
            abort(404);
        }

        if (! $this->userCanAccessDaret($user->id, $daret)) {
            abort(403);
        }

        $daret->load(['members.user.profile']);
        $cycle->load(['recipient', 'contributions.user']);

        $members = $daret->members()->with('user.profile')->orderBy('position_in_cycle')->get();

        return view('darets.cycle', [
            'daret' => $daret,
            'cycle' => $cycle,
            'members' => $members,
            'user' => $user,
        ]);
    }

    public function uploadReceipt(Request $request, Daret $daret, DaretCycle $cycle): RedirectResponse
    {
        $user = $request->user();

        if ($cycle->daret_id !== $daret->id) {
            abort(404);
        }

        if (! $daret->members()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        if ($cycle->recipient_id === $user->id) {
            abort(403, 'You are the recipient of this cycle and cannot upload a transfer.');
        }

        $data = $request->validate([
            'receipt' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $contribution = Contribution::updateOrCreate([
            'daret_id' => $daret->id,
            'daret_cycle_id' => $cycle->id,
            'user_id' => $user->id,
        ], [
            'amount' => $daret->contribution_amount,
            'paid_at' => now(),
            'status' => 'pending',
            'confirmed_by' => null,
            'confirmed_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'is_admin_override' => false,
        ]);

        $contribution->clearMediaCollection('receipt');
        $contribution->addMediaFromRequest('receipt')->toMediaCollection('receipt');

        $recipients = $daret->users()->where('users.id', '!=', $user->id)->get();

        Notification::send($recipients, new ReceiptUploaded($contribution));

        return redirect()->route('darets.cycles.show', [$daret, $cycle])
            ->with('status', 'Receipt uploaded and awaiting confirmation.');
    }

    public function confirm(Request $request, Contribution $contribution): RedirectResponse
    {
        $user = $request->user();
        $daret = $contribution->daret;
        $cycle = $contribution->cycle;

        if (! $this->userCanConfirmOrReject($user->id, $daret, $cycle)) {
            abort(403);
        }

        $contribution->status = 'confirmed';
        $contribution->confirmed_by = $user->id;
        $contribution->confirmed_at = now();
        $contribution->rejected_by = null;
        $contribution->rejected_at = null;
        $contribution->rejection_reason = null;
        $contribution->is_admin_override = $user->hasRole('admin');
        $contribution->save();

        StampReceiptPdf::dispatch($contribution->id);

        $memberCount = $daret->members()->count();
        $contributingMemberCount = $cycle->recipient_id ? $memberCount - 1 : $memberCount;
        $confirmedCount = $cycle->contributions()
            ->where('status', 'confirmed')
            ->distinct('user_id')
            ->count('user_id');

        $allMembersUploaded = $cycle->contributions()
            ->whereHas('media')
            ->distinct('user_id')
            ->count('user_id') >= $contributingMemberCount;

        $hasRecipient = $cycle->recipient_id !== null;

        if ($memberCount > 0 && $confirmedCount >= $contributingMemberCount && $allMembersUploaded && $hasRecipient) {
            $cycle->is_completed = true;
            $cycle->completed_at = now();
            $cycle->save();

            if ($daret->cycles()->where('is_completed', false)->count() === 0) {
                $daret->status = 'finished';
                $daret->save();
            }
        }

        $contribution->user->notify(new ReceiptStatusChanged($contribution));

        return back()->with('status', 'Contribution confirmed.');
    }

    public function reject(Request $request, Contribution $contribution): RedirectResponse
    {
        $user = $request->user();
        $daret = $contribution->daret;
        $cycle = $contribution->cycle;

        if (! $this->userCanConfirmOrReject($user->id, $daret, $cycle)) {
            abort(403);
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $contribution->status = 'rejected';
        $contribution->rejected_by = $user->id;
        $contribution->rejected_at = now();
        $contribution->rejection_reason = $data['reason'];
        $contribution->is_admin_override = $user->hasRole('admin');
        $contribution->save();

        $contribution->user->notify(new ReceiptStatusChanged($contribution));

        return back()->with('status', 'Contribution rejected.');
    }

    protected function userCanAccessDaret(int $userId, Daret $daret): bool
    {
        if ($daret->owner_id === $userId) {
            return true;
        }

        if ($daret->members()->where('user_id', $userId)->exists()) {
            return true;
        }

        $user = Auth::user();

        return $user && $user->hasRole('admin');
    }

    protected function userCanConfirmOrReject(int $userId, Daret $daret, ?DaretCycle $cycle = null): bool
    {
        // Admin can always confirm/reject
        $user = Auth::user();
        if ($user && $user->hasRole('admin')) {
            return true;
        }

        // Daret owner can confirm/reject
        if ($daret->owner_id === $userId) {
            return true;
        }

        // Cycle recipient can confirm/reject
        if ($cycle && $cycle->recipient_id === $userId) {
            return true;
        }

        return false;
    }

    public function viewReceipt(Request $request, Contribution $contribution): StreamedResponse
    {
        $user = $request->user();
        $daret = $contribution->daret;
        $cycle = $contribution->cycle;

        $canView = $user->hasRole('admin')
            || $daret->owner_id === $user->id
            || $cycle->recipient_id === $user->id
            || $contribution->user_id === $user->id;

        if (! $canView) {
            abort(403, 'You are not authorized to view this receipt.');
        }

        $media = $contribution->getFirstMedia('receipt');

        if (! $media) {
            abort(404, 'Receipt not found.');
        }

        return response()->streamDownload(function () use ($media) {
            echo file_get_contents($media->getPath());
        }, $media->file_name, [
            'Content-Type' => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
        ]);
    }
}
