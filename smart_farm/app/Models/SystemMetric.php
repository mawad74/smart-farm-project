<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMetric extends Model
{
    protected $fillable = [
        'farm_id',
        'type',
        'value',
        'timestamp',
    ];

    protected $casts = [
        'value' => 'float',
        'timestamp' => 'datetime',
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}