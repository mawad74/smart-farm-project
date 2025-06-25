<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'farm_id',
        'user_id',
        'type',
        // حذف 'generated_at' من هنا
    ];

    protected $casts = [
        // حذف 'generated_at' => 'datetime' من هنا
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function reportDetails()
    {
        return $this->hasMany(ReportDetail::class);
    }
}