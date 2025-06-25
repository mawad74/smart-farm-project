<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Http\Resources\SensorResource;
use App\Http\Resources\SensorDataResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SensorController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_farmer_manager');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();

            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $type = $request->input('type');
            $status = $request->input('status');

            $sensorsQuery = Sensor::where('farm_id', $farm->id)->with('farm', 'plant');

            if ($search) {
                $sensorsQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('location', 'like', "%{$search}%");
            }

            if ($type) {
                $sensorsQuery->where('type', $type);
            }

            if ($status) {
                $sensorsQuery->where('status', $status);
            }

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
            Log::error('Failed to retrieve sensors for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving sensors.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:temperature,humidity,soil_moisture,ph,nutrient',
                'plant_id' => 'required|exists:plants,id',
                'status' => 'required|in:active,inactive,faulty',
                'location' => 'nullable|string|max:255',
                'light_intensity' => 'nullable|numeric',
                'value' => 'nullable|numeric',
            ]);

            $sensor = Sensor::create([
                'farm_id' => $farm->id,
                'plant_id' => $request->plant_id,
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'location' => $request->location,
                'light_intensity' => $request->light_intensity,
                'value' => $request->value,
            ]);

            Log::info('Sensor created by Farmer Manager', ['sensor_id' => $sensor->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new SensorResource($sensor->load(['farm', 'plant'])),
                'message' => 'Sensor created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create sensor', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the sensor.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $sensor = Sensor::where('farm_id', $farm->id)->with('farm', 'plant')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new SensorResource($sensor),
                'message' => 'Sensor retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve sensor for farmer', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Sensor with ID {$id} not found or you don't have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function readings($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $sensor = Sensor::where('farm_id', $farm->id)->findOrFail($id);
            $readings = $sensor->sensorData()->latest()->paginate(10);
            return response()->json([
                'status' => 'success',
                'data' => SensorDataResource::collection($readings),
                'message' => 'Sensor readings retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve sensor readings for farmer', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Sensor with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $sensor = Sensor::where('farm_id', $farm->id)->findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:temperature,humidity,soil_moisture,ph,nutrient',
                'plant_id' => 'required|exists:plants,id',
                'status' => 'required|in:active,inactive,faulty',
                'location' => 'nullable|string|max:255',
                'light_intensity' => 'nullable|numeric',
                'value' => 'nullable|numeric',
            ]);

            $sensor->update($validated);

            Log::info('Sensor updated by Farmer Manager', ['sensor_id' => $sensor->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new SensorResource($sensor->load(['farm', 'plant'])),
                'message' => 'Sensor updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor update', ['sensor_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update sensor', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Sensor with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $sensor = Sensor::where('farm_id', $farm->id)->findOrFail($id);
            $sensor->delete();

            Log::info('Sensor deleted by Farmer Manager', ['sensor_id' => $id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'message' => "Sensor with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete sensor', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Sensor with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}