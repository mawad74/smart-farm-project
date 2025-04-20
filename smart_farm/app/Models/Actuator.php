<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actuator extends Model
{
    protected $fillable = [
        'farm_id',
        'plant_id',
        'type',
        'status',
        'action_type',
        'last_triggered_at',
    ];

    protected $casts = [
        'last_triggered_at' => 'datetime',
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function controlCommands()
    {
        return $this->hasMany(ControlCommand::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}