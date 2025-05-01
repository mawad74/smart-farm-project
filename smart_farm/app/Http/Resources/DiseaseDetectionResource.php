<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiseaseDetectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'plant_id' => $this->plant_id,
            'plant' => [
                'id' => $this->plant->id,
                'name' => $this->plant->name,
            ],
            'disease_name' => $this->disease_name,
            'confidence' => $this->confidence,
            'action_taken' => $this->action_taken,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}