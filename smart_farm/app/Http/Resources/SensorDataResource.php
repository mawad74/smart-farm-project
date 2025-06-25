<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SensorDataResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sensor_id' => $this->sensor_id,
            'soil_moisture_raw' => $this->soil_moisture_raw,
            'soil_moisture_percentage' => $this->soil_moisture_percentage,
            'moisture_status' => $this->moisture_status,
            'light_level_raw' => $this->light_level_raw,
            'light_percentage' => $this->light_percentage,
            'light_status' => $this->light_status,
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'timestamp' => $this->timestamp ? (is_string($this->timestamp) ? Carbon::parse($this->timestamp)->toDateTimeString() : $this->timestamp->toDateTimeString()) : null,
            'created_at' => $this->created_at ? (is_string($this->created_at) ? Carbon::parse($this->created_at)->toDateTimeString() : $this->created_at->toDateTimeString()) : null,
        ];
    }
}