<?php
namespace App\Http\Controllers\Api\Devices;
use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SensorDataController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'device_id' => 'required|string',
                'soil_moisture' => 'required|numeric',
                'light_level' => 'required|numeric',
                'temperature' => 'required|numeric',
                'humidity' => 'required|numeric',
                'timestamp' => 'required|date',
            ]);

            $sensor = Sensor::firstOrCreate(
                ['device_id' => $validated['device_id']],
                [
                    'name' => $validated['device_id'],
                    'type' => 'soil_moisture',
                    'status' => 'active',
                ]
            );

            $soilMoisturePercentage = $this->mapValue($validated['soil_moisture'], 0, 1023, 0, 100);
            $lightPercentage = $this->mapValue($validated['light_level'], 0, 1023, 0, 100);

            $moistureStatus = $this->getStatus($soilMoisturePercentage);
            $lightStatus = $this->getStatus($lightPercentage);

            $reading = SensorData::create([
                'sensor_id' => $sensor->id,
                'soil_moisture_raw' => $validated['soil_moisture'],
                'soil_moisture_percentage' => $soilMoisturePercentage,
                'moisture_status' => $moistureStatus,
                'light_level_raw' => $validated['light_level'],
                'light_percentage' => $lightPercentage,
                'light_status' => $lightStatus,
                'temperature' => $validated['temperature'],
                'humidity' => $validated['humidity'],
                'timestamp' => $validated['timestamp'],
            ]);

            $sensor->update(['value' => $validated['soil_moisture']]);

            Log::info('Sensor data stored successfully', [
                'reading_id' => $reading->id,
                'device_id' => $validated['device_id'],
                'sensor_id' => $sensor->id,
            ]);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'reading_id' => $reading->id,
                    'device_id' => $validated['device_id'],
                    'sensor_id' => $sensor->id,
                    'soil_moisture_percentage' => $soilMoisturePercentage,
                    'moisture_status' => $moistureStatus,
                    'light_percentage' => $lightPercentage,
                    'light_status' => $lightStatus,
                    'temperature' => $validated['temperature'],
                    'humidity' => $validated['humidity'],
                    'timestamp' => $validated['timestamp'],
                ],
                'message' => 'Sensor data stored successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor data', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to store sensor data', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while storing sensor data.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    private function mapValue($value, $in_min, $in_max, $out_min, $out_max)
    {
        return ($value - $in_min) * ($out_max - $out_min) / ($in_max - $in_min) + $out_min;
    }

    private function getStatus($percentage)
    {
        if ($percentage < 30) return 'Low';
        if ($percentage > 70) return 'High';
        return 'Normal';
    }
}