<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FinancialRecord extends Model
{
    protected $fillable = [
        'farm_id',
        'type',
        'value',
        'description',
        'timestamp',
    ];

    protected $casts = [
        'value' => 'float',
        'timestamp' => 'datetime',
    ];

    // Accessor للتأكد إن timestamp دايمًا كائن Carbon
    public function getTimestampAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}