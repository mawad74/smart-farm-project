<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'farm_id',
        'action',
        'status',
        'message',
        'timestamp',
        'logout_time',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'logout_time' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\User::class); // Corrected from \App\Models\User
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}