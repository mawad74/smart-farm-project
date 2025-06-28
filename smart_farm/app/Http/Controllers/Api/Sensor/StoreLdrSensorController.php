<?php

namespace App\Http\Controllers\Api\Sensor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sensor\StoreSensorDataRequest;
use App\Models\Farm;
use App\Services\Sensor\SensorService;
use Illuminate\Http\JsonResponse;

class StoreLdrSensorController extends Controller
{
    public function __invoke(StoreSensorDataRequest $request): JsonResponse
    {
        SensorService::make(Farm::first())
            ->setOrCreateLdrSensor()
            ->storeSensorData($request->validated()['value']);

        return response()->json([
            'status' => 'success',
        ]);
    }
}