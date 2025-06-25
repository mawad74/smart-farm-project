<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\WeatherData;
use App\Http\Resources\WeatherDataResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WeatherDataController extends Controller
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

            $weatherDataQuery = WeatherData::where('farm_id', $farm->id)->with('farm');

            if ($search) {
                $weatherDataQuery->where('temperature', 'like', "%{$search}%")
                                ->orWhere('rainfall', 'like', "%{$search}%")
                                ->orWhere('wind_speed', 'like', "%{$search}%");
            }

            if ($sort === 'newest') {
                $weatherDataQuery->latest();
            } elseif ($sort === 'oldest') {
                $weatherDataQuery->oldest();
            }

            $weatherData = $weatherDataQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => WeatherDataResource::collection($weatherData),
                'message' => 'Weather data retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve weather data for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving weather data.',
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
                'temperature' => 'required|numeric',
                'rainfall' => 'required|numeric|min:0',
                'wind_speed' => 'required|numeric|min:0',
                'timestamp' => 'required|date',
            ]);

            $weatherData = WeatherData::create([
                'farm_id' => $farm->id,
                'temperature' => $request->temperature,
                'rainfall' => $request->rainfall,
                'wind_speed' => $request->wind_speed,
                'timestamp' => $request->timestamp,
            ]);

            Log::info('Weather Data created by Farmer Manager', ['weather_data_id' => $weatherData->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new WeatherDataResource($weatherData->load('farm')),
                'message' => 'Weather data created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for weather data creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create weather data', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the weather data.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $weatherData = WeatherData::where('farm_id', $farm->id)->with('farm')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new WeatherDataResource($weatherData),
                'message' => 'Weather data retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve weather data for farmer', ['weather_data_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Weather data with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $weatherData = WeatherData::where('farm_id', $farm->id)->findOrFail($id);

            $validated = $request->validate([
                'temperature' => 'required|numeric',
                'rainfall' => 'required|numeric|min:0',
                'wind_speed' => 'required|numeric|min:0',
                'timestamp' => 'required|date',
            ]);

            $weatherData->update($validated);

            Log::info('Weather Data updated by Farmer Manager', ['weather_data_id' => $weatherData->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new WeatherDataResource($weatherData->load('farm')),
                'message' => 'Weather data updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for weather data update', ['weather_data_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update weather data', ['weather_data_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Weather data with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $weatherData = WeatherData::where('farm_id', $farm->id)->findOrFail($id);
            $weatherData->delete();

            Log::info('Weather Data deleted by Farmer Manager', ['weather_data_id' => $id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'message' => "Weather data with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete weather data', ['weather_data_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Weather data with ID {$id} not found or you do not have access to delete.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}