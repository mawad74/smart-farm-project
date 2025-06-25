<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorData extends Model
{
    protected $fillable = [
        'sensor_id',
        'value'
    ];

    protected $casts = [
        'value' => 'float',
    ];

    public $timestamps = false; // تعطيل الـ Timestamps

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
