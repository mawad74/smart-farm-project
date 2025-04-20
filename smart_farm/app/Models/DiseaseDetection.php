<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiseaseDetection extends Model
{
    protected $fillable = [
        'plant_id',
        'disease_name',
        'confidence',
        'action_taken',
    ];

    protected $casts = [
        'confidence' => 'float',
    ];

    // Relationships
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}