<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'farm_id' => $this->farm_id,
            'farm' => [
                'id' => $this->farm->id,
                'name' => $this->farm->name,
            ],
            'plant_id' => $this->plant_id,
            'plant' => [
                'id' => $this->plant->id,
                'name' => $this->plant->name,
            ],
            'actuator_id' => $this->actuator_id,
            'actuator' => [
                'id' => $this->actuator->id,
                'type' => $this->actuator->type,
            ],
            'schedule_time' => $this->schedule_time->toDateTimeString(),
            'status' => $this->status,
            'weather_forecast_integration' => $this->weather_forecast_integration,
            'priority_zone' => $this->priority_zone,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}