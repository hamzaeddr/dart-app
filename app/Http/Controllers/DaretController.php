<?php

namespace App\Http\Controllers;

use App\Models\Daret;
use App\Models\DaretMember;
use App\Models\DaretCycle;
use App\Models\Contribution;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DaretController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $darets = Daret::with(['owner'])
            ->withCount(['members', 'cycles'])
            ->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('members', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('darets.index', [
            'darets' => $darets,
            'user' => $user,
        ]);
    }

    public function create(Request $request): View
    {
        if (! $request->user()->hasRole('admin')) {
            abort(403, 'Only admins can create darets.');
        }

        return view('darets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('admin')) {
            abort(403, 'Only admins can create darets.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contribution_amount' => ['required', 'numeric', 'min:0.01'],
            'period' => ['required', 'in:weekly,monthly'],
            'total_members' => ['required', 'integer', 'min:1', 'max:100'],
            'start_date' => ['required', 'date'],
        ]);

        $daret = Daret::create([
            'owner_id' => $user->id,
            'name' => $data['name'],
            'contribution_amount' => $data['contribution_amount'],
            'period' => $data['period'],
            'total_members' => $data['total_members'],
            'start_date' => $data['start_date'],
            'schedule' => null,
            'status' => 'active',
        ]);

        DaretMember::create([
            'daret_id' => $daret->id,
            'user_id' => $user->id,
            'position_in_cycle' => 1,
            'joined_at' => now(),
        ]);

        return redirect()->route('darets.show', $daret);
    }

    public function show(Request $request, Daret $daret): View
    {
        $user = $request->user();

        if (! $this->userCanAccessDaret($user->id, $daret)) {
            abort(403);
        }

        $daret->load([
            'owner',
            'members.user.profile',
            'cycles.recipient',
            'cycles.contributions.user',
        ]);

        $cycles = $daret->cycles->sortBy('cycle_number');

        return view('darets.show', [
            'daret' => $daret,
            'cycles' => $cycles,
            'user' => $user,
        ]);
    }

    public function join(Request $request, Daret $daret): RedirectResponse
    {
        $user = $request->user();

        if ($daret->status !== 'active') {
            return redirect()->route('darets.show', $daret)->with('error', 'This daret is not active.');
        }

        if ($daret->members()->where('user_id', $user->id)->exists()) {
            return redirect()->route('darets.show', $daret)->with('status', 'You are already a member of this daret.');
        }

        $currentCount = $daret->members()->count();

        if ($currentCount >= (int) $daret->total_members) {
            return redirect()->route('darets.show', $daret)->with('error', 'This daret is already full.');
        }

        $position = $daret->members()->max('position_in_cycle') + 1;

        DaretMember::create([
            'daret_id' => $daret->id,
            'user_id' => $user->id,
            'position_in_cycle' => $position,
            'joined_at' => now(),
        ]);

        if ($daret->members()->count() === (int) $daret->total_members) {
            $daret->generateCycles();
        }

        return redirect()->route('darets.show', $daret)->with('status', 'You have joined this daret.');
    }

    public function addMember(Request $request, Daret $daret): RedirectResponse
    {
        $currentUser = $request->user();

        if ($currentUser->id !== $daret->owner_id && ! $currentUser->hasRole('admin')) {
            abort(403);
        }

        if ($daret->status !== 'active') {
            return redirect()->route('darets.show', $daret)->with('error', 'This daret is not active.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $userToAdd = User::where('email', $validated['email'])->first();

        if (! $userToAdd) {
            return redirect()->route('darets.show', $daret)->with('error', 'User not found.');
        }

        if ($daret->members()->where('user_id', $userToAdd->id)->exists()) {
            return redirect()->route('darets.show', $daret)->with('status', 'This user is already a member of this daret.');
        }

        $currentCount = $daret->members()->count();

        // If daret is full, admin can still add members by increasing total_members
        if ($currentCount >= (int) $daret->total_members) {
            if (! $currentUser->hasRole('admin')) {
                return redirect()->route('darets.show', $daret)->with('error', 'This daret is already full.');
            }
            // Admin is adding beyond capacity - increase total_members
            $daret->total_members = $currentCount + 1;
            $daret->save();
        }

        $maxPosition = $daret->members()->max('position_in_cycle');
        $position = $maxPosition ? $maxPosition + 1 : 1;

        DaretMember::create([
            'daret_id' => $daret->id,
            'user_id' => $userToAdd->id,
            'position_in_cycle' => $position,
            'joined_at' => now(),
        ]);

        // Regenerate cycles if we now have enough members
        if ($daret->members()->count() === (int) $daret->total_members) {
            $daret->generateCycles();
        }

        return redirect()->route('darets.show', $daret)->with('status', 'Member added to this daret.');
    }

    public function updateRecipientOrder(Request $request, Daret $daret): RedirectResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('admin')) {
            abort(403, 'Only admins can change the recipient order.');
        }

        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['required', 'integer', 'exists:users,id'],
        ]);

        $memberUserIds = $daret->members()->pluck('user_id')->toArray();
        $providedUserIds = $validated['user_ids'];

        if (count($providedUserIds) !== count($memberUserIds) || array_diff($providedUserIds, $memberUserIds)) {
            return redirect()->route('darets.show', $daret)
                ->with('error', 'Invalid user IDs provided. All members must be included.');
        }

        $daret->updateRecipientOrder($providedUserIds);

        return redirect()->route('darets.show', $daret)
            ->with('status', 'Recipient order updated successfully.');
    }

    public function destroy(Request $request, Daret $daret): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('admin')) {
            abort(403, 'Only admins can delete darets.');
        }

        $daret->delete();

        return redirect()->route('darets.index')->with('status', 'Daret deleted successfully.');
    }

    public function removeMember(Request $request, Daret $daret, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('admin')) {
            abort(403, 'Only admins can remove members.');
        }

        if ($daret->owner_id === $user->id) {
            return redirect()->route('darets.show', $daret)->with('error', 'Cannot remove the daret owner.');
        }

        $member = $daret->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return redirect()->route('darets.show', $daret)->with('error', 'User is not a member of this daret.');
        }

        $member->delete();

        // Update total_members count
        $daret->total_members = max(1, $daret->members()->count());
        $daret->save();

        return redirect()->route('darets.show', $daret)->with('status', 'Member removed from daret.');
    }

    public function update(Request $request, Daret $daret): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasRole('admin')) {
            abort(403, 'Only admins can update darets.');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'contribution_amount' => ['sometimes', 'numeric', 'min:0.01'],
            'period' => ['sometimes', 'in:weekly,monthly'],
        ]);

        $daret->update($validated);

        return redirect()->route('darets.show', $daret)->with('status', 'Daret updated successfully.');
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
}
