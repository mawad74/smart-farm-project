<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'farm_id',
        'sensor_id',
        'user_id',
        'message',
        'status',
        'priority',
        'action_taken',
        'channel',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class); // Corrected from \App\Models\User
    }
}