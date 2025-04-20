<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'farm_id',
        'user_id',
        'type',
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class); // Corrected from \App\Models\User
    }

    public function reportDetails()
    {
        return $this->hasMany(ReportDetail::class);
    }
}