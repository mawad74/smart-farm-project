<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ControlCommandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'actuator_id' => $this->actuator_id,
            'actuator' => [
                'id' => $this->actuator->id,
                'type' => $this->actuator->type,
            ],
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'command_type' => $this->command_type,
            'executed_at' => $this->executed_at->toDateTimeString(),
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}