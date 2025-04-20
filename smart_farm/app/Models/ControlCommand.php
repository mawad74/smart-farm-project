<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlCommand extends Model
{
    protected $fillable = [
        'actuator_id',
        'user_id',
        'command_type',
        'executed_at',
        'status',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'status' => 'boolean',
    ];

    // Relationships
    public function actuator()
    {
        return $this->belongsTo(Actuator::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class); // Corrected from \App\Models\User
    }
}