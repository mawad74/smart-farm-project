<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceUsage extends Model
{
    protected $fillable = [
        'farm_id',
        'plant_id',
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

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}