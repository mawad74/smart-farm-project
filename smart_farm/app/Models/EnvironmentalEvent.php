<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvironmentalEvent extends Model
{
    protected $fillable = [
        'farm_id',
        'type',
        'start_time',
        'end_time',
        'severity',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}