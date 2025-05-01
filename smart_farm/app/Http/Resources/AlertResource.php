<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
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
            'sensor_id' => $this->sensor_id,
            'sensor' => $this->sensor ? [
                'id' => $this->sensor->id,
                'type' => $this->sensor->type,
            ] : null,
            'user_id' => $this->user_id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'message' => $this->message,
            'status' => $this->status,
            'priority' => $this->priority,
            'action_taken' => $this->action_taken,
            'channel' => $this->channel,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}