<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\StampReceiptPdf;
use App\Models\Contribution;
use App\Models\Daret;
use App\Models\DaretCycle;
use App\Notifications\ReceiptStatusChanged;
use App\Notifications\ReceiptUploaded;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ContributionApiController extends Controller
{
    public function index(Request $request, Daret $daret): JsonResponse
    {
        $user = $request->user();

        if (! $this->userCanAccessDaret($user->id, $daret)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $contributions = $daret->contributions()->with(['cycle', 'user'])->get();

        return response()->json($contributions);
    }

    public function uploadReceipt(Request $request, Daret $daret, DaretCycle $cycle): JsonResponse
    {
        $user = $request->user();

        if ($cycle->daret_id !== $daret->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if (! $daret->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($cycle->recipient_id === $user->id) {
            return response()->json(['message' => 'You are the recipient of this cycle and cannot upload a transfer.'], 403);
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

        return response()->json($contribution->fresh(), 201);
    }

    public function confirm(Request $request, Contribution $contribution): JsonResponse
    {
        $user = $request->user();
        $daret = $contribution->daret;
        $cycle = $contribution->cycle;

        if (! $this->userCanConfirmOrReject($user->id, $daret)) {
            return response()->json(['message' => 'Forbidden'], 403);
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

        return response()->json($contribution->fresh());
    }

    public function reject(Request $request, Contribution $contribution): JsonResponse
    {
        $user = $request->user();
        $daret = $contribution->daret;

        if (! $this->userCanConfirmOrReject($user->id, $daret)) {
            return response()->json(['message' => 'Forbidden'], 403);
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

        return response()->json($contribution->fresh());
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

    protected function userCanConfirmOrReject(int $userId, Daret $daret): bool
    {
        return $this->userCanAccessDaret($userId, $daret);
    }
}
