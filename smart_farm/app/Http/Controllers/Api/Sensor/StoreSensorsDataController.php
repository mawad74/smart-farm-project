<?php

namespace App\Http\Controllers\Api\Sensor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sensor\StoreSensorDataRequest;
use App\Models\Farm;
use App\Services\Sensor\SensorService;
use Illuminate\Http\JsonResponse;

class StoreSensorsDataController extends Controller
{
    public function __invoke(StoreSensorDataRequest $request): JsonResponse
    {
        $data = $request->validated();

        SensorService::make(Farm::first())
            ->storeTemperatureSensorData($data['temperature'])
            ->storeHumiditySensorData($data['humidity'])
            ->storeLdrSensorData($data['ldr'])
            ->storeSoilMoistureSensorData($data['soil']);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
