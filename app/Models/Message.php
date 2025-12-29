<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'daret_id',
        'user_id',
        'body',
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
