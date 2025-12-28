<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Daret;
use App\Models\User;
use App\Models\Contribution;

class DaretCycle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'daret_id',
        'cycle_number',
        'due_date',
        'recipient_id',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'bool',
        'completed_at' => 'datetime',
    ];

    public function daret()
    {
        return $this->belongsTo(Daret::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'daret_cycle_id');
    }
}
