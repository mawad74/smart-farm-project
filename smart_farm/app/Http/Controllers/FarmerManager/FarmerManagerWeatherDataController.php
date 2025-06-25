<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\WeatherData;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FarmerManagerWeatherDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('farmer_manager'); // Middleware للـ Farmer Manager
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');

        $weatherDataQuery = WeatherData::where('farm_id', $farm->id);

        if ($search) {
            $weatherDataQuery->where('timestamp', 'like', "%{$search}%")
                             ->orWhere('temperature', 'like', "%{$search}%")
                             ->orWhere('rainfall', 'like', "%{$search}%")
                             ->orWhere('wind_speed', 'like', "%{$search}%");
        }

        if ($sort === 'newest') {
            $weatherDataQuery->latest('timestamp');
        } elseif ($sort === 'oldest') {
            $weatherDataQuery->oldest('timestamp');
        }

        $weatherData = $weatherDataQuery->paginate(10);
        $farms = Farm::where('user_id', $user->id)->get();

        return view('farmer_manager.weather-data.index', compact('weatherData', 'farms'));
    }

    public function create()
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        return view('farmer_manager.weather-data.create', compact('farm'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();

        try {
            $validated = $request->validate([
                'date' => 'required|date', // نستخدم date كمدخل من الفورم
                'temperature' => 'required|numeric',
                'rainfall' => 'required|numeric',
                'wind_speed' => 'nullable|numeric',
            ]);

            // تحويل date لـ timestamp
            $timestamp = Carbon::parse($request->date)->toDateTimeString();

            $weatherData = WeatherData::create([
                'farm_id' => $farm->id,
                'timestamp' => $timestamp, // استخدام timestamp بدل date
                'temperature' => $request->temperature,
                'rainfall' => $request->rainfall,
                'wind_speed' => $request->wind_speed,
            ]);

            Log::info('Weather Data created by Farmer Manager', [
                'weather_data_id' => $weatherData->id,
                'farm_id' => $farm->id,
                'user_id' => $user->id,
                'input_data' => $request->all(),
            ]);
            return redirect()->route('farmer_manager.weather-data.index')->with('success', 'Weather data created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for weather data creation', [
                'errors' => $e->errors(),
                'user_id' => $user->id,
                'input_data' => $request->all(),
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create weather data', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'input_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Failed to create weather data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $weatherData = WeatherData::where('farm_id', $farm->id)->findOrFail($id);
        return view('farmer_manager.weather-data.edit', compact('weatherData'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $weatherData = WeatherData::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'temperature' => 'required|numeric',
                'rainfall' => 'required|numeric',
                'wind_speed' => 'nullable|numeric',
            ]);

            $timestamp = Carbon::parse($request->date)->toDateTimeString();

            $weatherData->update([
                'timestamp' => $timestamp,
                'temperature' => $request->temperature,
                'rainfall' => $request->rainfall,
                'wind_speed' => $request->wind_speed,
            ]);

            Log::info('Weather Data updated by Farmer Manager', [
                'weather_data_id' => $weatherData->id,
                'farm_id' => $farm->id,
                'user_id' => $user->id,
            ]);
            return redirect()->route('farmer_manager.weather-data.index')->with('success', 'Weather data updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for weather data update', [
                'weather_data_id' => $weatherData->id,
                'errors' => $e->errors(),
                'user_id' => $user->id,
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update weather data', [
                'weather_data_id' => $weatherData->id,
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return redirect()->back()->with('error', 'Failed to update weather data.');
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $weatherData = WeatherData::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $weatherData->delete();
            Log::info('Weather Data deleted by Farmer Manager', [
                'weather_data_id' => $id,
                'farm_id' => $farm->id,
                'user_id' => $user->id,
            ]);
            return redirect()->route('farmer_manager.weather-data.index')->with('success', 'Weather data deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete weather data', [
                'weather_data_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return redirect()->route('farmer_manager.weather-data.index')->with('error', 'Failed to delete weather data.');
        }
    }
}