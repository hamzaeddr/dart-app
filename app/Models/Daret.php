<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\DaretMember;
use App\Models\DaretCycle;
use App\Models\Contribution;

class Daret extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'name',
        'contribution_amount',
        'period',
        'total_members',
        'start_date',
        'schedule',
        'status',
    ];

    protected $casts = [
        'contribution_amount' => 'decimal:2',
        'start_date' => 'date',
        'schedule' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->hasMany(DaretMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'daret_members')
            ->withPivot(['position_in_cycle', 'joined_at'])
            ->withTimestamps();
    }

    public function cycles()
    {
        return $this->hasMany(DaretCycle::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * Generate the cycle schedule and recipients once the daret is full.
     * Shuffles member positions randomly before generating cycles.
     */
    public function generateCycles(): void
    {
        $members = $this->members()->get();

        if ($members->count() !== (int) $this->total_members) {
            return;
        }

        $this->shuffleMemberPositions();

        $members = $this->members()->orderBy('position_in_cycle')->get();

        $this->cycles()->delete();

        $startDate = Carbon::parse($this->start_date);

        foreach ($members as $index => $member) {
            $dueDate = $this->period === 'weekly'
                ? $startDate->copy()->addWeeks($index)
                : $startDate->copy()->addMonths($index);

            $this->cycles()->create([
                'cycle_number' => $index + 1,
                'due_date' => $dueDate,
                'recipient_id' => $member->user_id,
            ]);
        }
    }

    /**
     * Shuffle member positions randomly.
     */
    public function shuffleMemberPositions(): void
    {
        $members = $this->members()->get();
        $positions = range(1, $members->count());
        shuffle($positions);

        foreach ($members as $index => $member) {
            $member->position_in_cycle = $positions[$index];
            $member->save();
        }
    }

    /**
     * Update the recipient order for cycles (admin only).
     * Accepts an array of user_ids in the desired order.
     */
    public function updateRecipientOrder(array $userIds): void
    {
        $members = $this->members()->get()->keyBy('user_id');

        foreach ($userIds as $position => $userId) {
            if ($members->has($userId)) {
                $members[$userId]->position_in_cycle = $position + 1;
                $members[$userId]->save();
            }
        }

        $this->regenerateCyclesWithCurrentOrder();
    }

    /**
     * Regenerate cycles using current member positions (without shuffling).
     */
    public function regenerateCyclesWithCurrentOrder(): void
    {
        $members = $this->members()->orderBy('position_in_cycle')->get();

        if ($members->count() !== (int) $this->total_members) {
            return;
        }

        $this->cycles()->delete();

        $startDate = Carbon::parse($this->start_date);

        foreach ($members as $index => $member) {
            $dueDate = $this->period === 'weekly'
                ? $startDate->copy()->addWeeks($index)
                : $startDate->copy()->addMonths($index);

            $this->cycles()->create([
                'cycle_number' => $index + 1,
                'due_date' => $dueDate,
                'recipient_id' => $member->user_id,
            ]);
        }
    }
}
