<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SensorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
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
            'status' => $this->status,
            'location' => $this->location,
            'light_intensity' => $this->light_intensity,
            'value' => $this->value,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}