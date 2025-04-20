<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'farm_id',
        'parameter',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}