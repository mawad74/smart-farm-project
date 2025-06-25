<?php

namespace App\Http\Requests\Sensor;

use Illuminate\Foundation\Http\FormRequest;

class StoreSensorDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'value' => ['required', 'numeric'],
        ];
    }
}
