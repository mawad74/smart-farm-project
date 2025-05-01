<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SensorDataResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sensor_id' => $this->sensor_id,
            'timestamp' => $this->timestamp->toDateTimeString(),
            'value' => $this->value,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}