<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'farm_id',
        'plant_id',
        'actuator_id',
        'schedule_time',
        'status',
        'weather_forecast_integration',
        'priority_zone',
    ];

    protected $casts = [
        'schedule_time' => 'datetime',
        'weather_forecast_integration' => 'boolean',
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

    public function actuator()
    {
        return $this->belongsTo(Actuator::class);
    }
}