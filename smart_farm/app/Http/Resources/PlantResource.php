<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlantResource extends JsonResource
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
            'health_status' => $this->health_status,
            'growth_rate' => $this->growth_rate,
            'yield_prediction' => $this->yield_prediction,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}