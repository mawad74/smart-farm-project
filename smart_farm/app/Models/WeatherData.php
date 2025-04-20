<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    protected $fillable = [
        'farm_id',
        'temperature',
        'rainfall',
        'wind_speed',
        'timestamp',
    ];

    protected $casts = [
        'temperature' => 'float',
        'rainfall' => 'float',
        'wind_speed' => 'float',
        'timestamp' => 'datetime',
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}