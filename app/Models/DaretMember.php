<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Daret;
use App\Models\User;

class DaretMember extends Model
{
	use HasFactory;

	protected $table = 'daret_members';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'daret_id',
		'user_id',
		'position_in_cycle',
		'joined_at',
	];

	protected $casts = [
		'joined_at' => 'datetime',
	];

	public function daret()
	{
		return $this->belongsTo(Daret::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
