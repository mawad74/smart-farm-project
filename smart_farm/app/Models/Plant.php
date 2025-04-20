<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    protected $fillable = [
        'farm_id',
        'name',
        'type',
        'health_status',
        'growth_rate',
        'yield_prediction',
    ];

    // Relationships
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }

    public function actuators()
    {
        return $this->hasMany(Actuator::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function diseaseDetections()
    {
        return $this->hasMany(DiseaseDetection::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function resourceUsages()
    {
        return $this->hasMany(ResourceUsage::class);
    }
}