<?php

namespace App\Http\Requests\Sensor;

use Illuminate\Foundation\Http\FormRequest;

class StoreSensorDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'temperature' => ['required', 'numeric'],
            'humidity' => ['required', 'numeric'],
            'soil' => ['required', 'numeric'],
            'ldr' => ['required', 'numeric']
        ];
    }
}
