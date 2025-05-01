<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Http\Resources\SensorResource;
use App\Http\Resources\SensorDataResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $type = $request->input('type');
            $farm_id = $request->input('farm_id');
            $status = $request->input('status');

            $sensorsQuery = Sensor::with('farm', 'plant');

            // Search
            if ($search) {
                $sensorsQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('location', 'like', "%{$search}%");
            }

            // Filter by type
            if ($type) {
                $sensorsQuery->where('type', $type);
            }

            // Filter by farm
            if ($farm_id) {
                $sensorsQuery->where('farm_id', $farm_id);
            }

            // Filter by status
            if ($status) {
                $sensorsQuery->where('status', $status);
            }

            // Sort
            if ($sort === 'newest') {
                $sensorsQuery->latest();
            } elseif ($sort === 'oldest') {
                $sensorsQuery->oldest();
            }

            $sensors = $sensorsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => SensorResource::collection($sensors),
                'message' => 'Sensors retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve sensors', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving sensors. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:temperature,humidity,soil_moisture,ph,nutrient',
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'status' => 'required|in:active,inactive,faulty',
                'location' => 'nullable|string|max:255',
                'light_intensity' => 'nullable|numeric',
                'value' => 'nullable|numeric',
            ]);

            $sensor = new Sensor();
            $sensor->name = $request->name;
            $sensor->type = $request->type;
            $sensor->farm_id = $request->farm_id;
            $sensor->plant_id = $request->plant_id;
            $sensor->status = $request->status;
            $sensor->location = $request->location;
            $sensor->light_intensity = $request->light_intensity;
            $sensor->value = $request->value;
            $sensor->save();

            Log::info('Sensor created successfully', ['sensor_id' => $sensor->id]);
            return response()->json([
                'status' => 'success',
                'data' => new SensorResource($sensor->load(['farm', 'plant'])),
                'message' => 'Sensor created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create sensor', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the sensor. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $sensor = Sensor::with('farm', 'plant')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new SensorResource($sensor),
                'message' => 'Sensor retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve sensor', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Sensor with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function readings($id)
    {
        try {
            $sensor = Sensor::findOrFail($id);
            $readings = $sensor->sensorData()->latest()->paginate(10);
            return response()->json([
                'status' => 'success',
                'data' => SensorDataResource::collection($readings),
                'message' => 'Sensor readings retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve sensor readings', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Sensor with ID {$id} not found or an unexpected error occurred while retrieving readings. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $sensor = Sensor::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:temperature,humidity,soil_moisture,ph,nutrient',
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'status' => 'required|in:active,inactive,faulty',
                'location' => 'nullable|string|max:255',
                'light_intensity' => 'nullable|numeric',
                'value' => 'nullable|numeric',
            ]);

            $sensor->name = $request->name;
            $sensor->type = $request->type;
            $sensor->farm_id = $request->farm_id;
            $sensor->plant_id = $request->plant_id;
            $sensor->status = $request->status;
            $sensor->location = $request->location;
            $sensor->light_intensity = $request->light_intensity;
            $sensor->value = $request->value;
            $sensor->save();

            Log::info('Sensor updated successfully', ['sensor_id' => $sensor->id]);
            return response()->json([
                'status' => 'success',
                'data' => new SensorResource($sensor->load(['farm', 'plant'])),
                'message' => 'Sensor updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor update', ['sensor_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update sensor', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Sensor with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $sensor = Sensor::findOrFail($id);
            $sensor->delete();

            Log::info('Sensor deleted successfully', ['sensor_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Sensor with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete sensor', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Sensor with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}