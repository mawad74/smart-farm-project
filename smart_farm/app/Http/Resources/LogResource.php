<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'farm_id' => $this->farm_id,
            'farm' => $this->farm ? [
                'id' => $this->farm->id,
                'name' => $this->farm->name,
            ] : null,
            'action' => $this->action,
            'status' => $this->status,
            'message' => $this->message,
            'timestamp' => $this->timestamp->toDateTimeString(),
            'logout_time' => $this->logout_time ? $this->logout_time->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}