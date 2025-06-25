<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = [
        'sensor_id',
        'soil_moisture_raw',
        'soil_moisture_percentage',
        'moisture_status',
        'light_level_raw',
        'light_percentage',
        'light_status',
        'temperature',
        'humidity',
        'timestamp',
    ];

    protected $casts = [
        'soil_moisture_raw' => 'integer',
        'soil_moisture_percentage' => 'float',
        'light_level_raw' => 'integer',
        'light_percentage' => 'float',
        'temperature' => 'float',
        'humidity' => 'float',
        'timestamp' => 'datetime',
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}