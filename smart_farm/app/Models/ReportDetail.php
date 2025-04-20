<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportDetail extends Model
{
    protected $fillable = [
        'report_id',
        'category',
        'value',
        'description',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    // Relationships
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}