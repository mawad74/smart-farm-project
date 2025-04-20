<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = [
        'name',
        'farm_id',
        'plant_id',
        'type',
        'value',
        'status',
        'location',
        'light_intensity',
    ];

    protected $casts = [
        'value' => 'float',
        'light_intensity' => 'float',
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

    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}