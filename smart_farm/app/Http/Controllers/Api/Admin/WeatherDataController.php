<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeatherData;
use App\Http\Resources\WeatherDataResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class WeatherDataController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $farm_id = $request->input('farm_id');

            $weatherDataQuery = WeatherData::with('farm');

            if ($search) {
                $weatherDataQuery->where('temperature', 'like', "%{$search}%")
                                ->orWhere('rainfall', 'like', "%{$search}%")
                                ->orWhere('wind_speed', 'like', "%{$search}%");
            }

            if ($farm_id) {
                $weatherDataQuery->where('farm_id', $farm_id);
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
            Log::error('Failed to retrieve weather data', ['error' => $e->getMessage()]);
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
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'temperature' => 'required|numeric',
                'rainfall' => 'required|numeric|min:0',
                'wind_speed' => 'required|numeric|min:0',
                'timestamp' => 'required|date',
            ]);

            $weatherData = WeatherData::create($validated);

            Log::info('Weather Data created by Admin', ['weather_data_id' => $weatherData->id]);
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
            $weatherData = WeatherData::with('farm')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new WeatherDataResource($weatherData),
                'message' => 'Weather data retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve weather data', ['weather_data_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Weather data with ID {$id} not found.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $weatherData = WeatherData::findOrFail($id);

            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'temperature' => 'required|numeric',
                'rainfall' => 'required|numeric|min:0',
                'wind_speed' => 'required|numeric|min:0',
                'timestamp' => 'required|date',
            ]);

            $weatherData->update($validated);

            Log::info('Weather Data updated by Admin', ['weather_data_id' => $weatherData->id]);
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
            $weatherData = WeatherData::findOrFail($id);
            $weatherData->delete();

            Log::info('Weather Data deleted by Admin', ['weather_data_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Weather data with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete weather data', ['weather_data_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Weather data with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}