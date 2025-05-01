<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActuatorResource extends JsonResource
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
            'type' => $this->type,
            'status' => $this->status,
            'action_type' => $this->action_type,
            'last_triggered_at' => $this->last_triggered_at ? $this->last_triggered_at->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}