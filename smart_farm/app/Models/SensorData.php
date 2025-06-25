<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
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
        'timestamp' => 'datetime',
        'soil_moisture_raw' => 'float',
        'soil_moisture_percentage' => 'float',
        'light_level_raw' => 'float',
        'light_percentage' => 'float',
        'temperature' => 'float',
        'humidity' => 'float',
    ];

    public $timestamps = false; // تعطيل الـ Timestamps

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}