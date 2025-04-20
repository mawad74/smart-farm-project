<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeatherData;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminWeatherDataController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $farm_id = $request->input('farm_id');

        $weatherDataQuery = WeatherData::with('farm');

        // البحث
        if ($search) {
            $weatherDataQuery->where('temperature', 'like', "%{$search}%")
                            ->orWhere('rainfall', 'like', "%{$search}%")
                            ->orWhere('wind_speed', 'like', "%{$search}%");
        }

        // فلترة حسب المزرعة
        if ($farm_id) {
            $weatherDataQuery->where('farm_id', $farm_id);
        }

        // الترتيب
        if ($sort === 'newest') {
            $weatherDataQuery->latest();
        } elseif ($sort === 'oldest') {
            $weatherDataQuery->oldest();
        }

        $weatherData = $weatherDataQuery->paginate(10);
        $farms = Farm::all(); // جلب كل المزارع للفلترة

        return view('admin.weather-data.index', compact('weatherData', 'farms'));
    }

    public function create()
    {
        $farms = Farm::all();
        return view('admin.weather-data.create', compact('farms'));
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

            $weatherData = new WeatherData();
            $weatherData->farm_id = $request->farm_id;
            $weatherData->temperature = $request->temperature;
            $weatherData->rainfall = $request->rainfall;
            $weatherData->wind_speed = $request->wind_speed;
            $weatherData->timestamp = $request->timestamp;
            $weatherData->save();

            Log::info('Weather Data created successfully', ['weather_data_id' => $weatherData->id]);
            return redirect()->route('admin.weather-data.index')->with('success', 'Weather Data created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for weather data creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create weather data', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create weather data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $weatherData = WeatherData::findOrFail($id);
        $farms = Farm::all();
        return view('admin.weather-data.edit', compact('weatherData', 'farms'));
    }

    public function update(Request $request, $id)
    {
        $weatherData = WeatherData::findOrFail($id);

        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'temperature' => 'required|numeric',
                'rainfall' => 'required|numeric|min:0',
                'wind_speed' => 'required|numeric|min:0',
                'timestamp' => 'required|date',
            ]);

            $weatherData->farm_id = $request->farm_id;
            $weatherData->temperature = $request->temperature;
            $weatherData->rainfall = $request->rainfall;
            $weatherData->wind_speed = $request->wind_speed;
            $weatherData->timestamp = $request->timestamp;
            $weatherData->save();

            Log::info('Weather Data updated successfully', ['weather_data_id' => $weatherData->id]);
            return redirect()->route('admin.weather-data.index')->with('success', 'Weather Data updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for weather data update', ['weather_data_id' => $weatherData->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update weather data', ['weather_data_id' => $weatherData->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update weather data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $weatherData = WeatherData::findOrFail($id);
            $weatherData->delete();

            Log::info('Weather Data deleted successfully', ['weather_data_id' => $id]);
            return redirect()->route('admin.weather-data.index')->with('success', 'Weather Data deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete weather data', ['weather_data_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.weather-data.index')->with('error', 'Failed to delete weather data: ' . $e->getMessage());
        }
    }
}