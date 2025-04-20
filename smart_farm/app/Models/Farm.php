<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable = [
        'name',
        'location',
        'user_id',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\User::class); // Corrected from \App\Models\User
    }

    public function plants()
    {
        return $this->hasMany(Plant::class);
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

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function resourceUsages()
    {
        return $this->hasMany(ResourceUsage::class);
    }

    public function financialRecords()
    {
        return $this->hasMany(FinancialRecord::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function weatherData()
    {
        return $this->hasMany(WeatherData::class);
    }

    public function environmentalEvents()
    {
        return $this->hasMany(EnvironmentalEvent::class);
    }

    public function systemMetrics()
    {
        return $this->hasMany(SystemMetric::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}