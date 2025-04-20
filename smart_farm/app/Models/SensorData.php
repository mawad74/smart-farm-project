<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $fillable = [
        'sensor_id',
        'timestamp',
        'value',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'value' => 'float',
    ];

    // Relationships
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}