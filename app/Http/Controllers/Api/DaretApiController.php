<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Daret;
use App\Models\DaretMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaretApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $darets = Daret::with('owner')
            ->withCount(['members', 'cycles'])
            ->where(function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhereHas('members', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($darets);
    }

    public function show(Request $request, Daret $daret): JsonResponse
    {
        $user = $request->user();

        if (! $this->userCanAccessDaret($user->id, $daret)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $daret->load([
            'owner',
            'members.user.profile',
            'cycles.recipient',
            'cycles.contributions.user',
        ]);

        return response()->json($daret);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

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

        return response()->json($daret->fresh(), 201);
    }

    public function join(Request $request, Daret $daret): JsonResponse
    {
        $user = $request->user();

        if ($daret->status !== 'active') {
            return response()->json(['message' => 'Daret is not active'], 422);
        }

        if ($daret->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already a member'], 200);
        }

        $currentCount = $daret->members()->count();

        if ($currentCount >= (int) $daret->total_members) {
            return response()->json(['message' => 'Daret is full'], 422);
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

        return response()->json($daret->fresh()->load(['members.user', 'cycles']));
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
