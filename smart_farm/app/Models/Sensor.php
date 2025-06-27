<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    protected $fillable = [
        'type',
        'name',
        'device_id',
        'unit',
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

    public function farm()
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function plant()
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }

    public function sensorData()
    public function sensorData(): HasMany
    {
        return $this->hasMany(SensorData::class);
    }
}
