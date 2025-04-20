<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'farm_id',
        'plant_id',
        'type',
        'status',
        'completion_rate',
        'time_taken',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'completion_rate' => 'float',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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
}