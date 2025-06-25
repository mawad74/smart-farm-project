<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportRequest extends Model
{
    protected $fillable = [
        'user_id',
        'farm_id',
        'type',
        'status',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}