<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\Farm;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminSensorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $type = $request->input('type');
        $farm_id = $request->input('farm_id');
        $status = $request->input('status');

        $sensorsQuery = Sensor::with('farm', 'plant');

        // البحث
        if ($search) {
            $sensorsQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('location', 'like', "%{$search}%");
        }

        // فلترة حسب النوع
        if ($type) {
            $sensorsQuery->where('type', $type);
        }

        // فلترة حسب المزرعة
        if ($farm_id) {
            $sensorsQuery->where('farm_id', $farm_id);
        }

        // فلترة حسب الحالة
        if ($status) {
            $sensorsQuery->where('status', $status);
        }

        // الترتيب
        if ($sort === 'newest') {
            $sensorsQuery->latest();
        } elseif ($sort === 'oldest') {
            $sensorsQuery->oldest();
        }

        $sensors = $sensorsQuery->paginate(10);
        $farms = Farm::all(); // جلب كل المزارع للفلترة
        return view('admin.sensors.index', compact('sensors', 'farms'));
    }

    public function create()
    {
        $farms = Farm::all();
        $plants = Plant::all();
        return view('admin.sensors.create', compact('farms', 'plants'));
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
            ]);

            $sensor = new Sensor();
            $sensor->name = $request->name;
            $sensor->type = $request->type;
            $sensor->farm_id = $request->farm_id;
            $sensor->plant_id = $request->plant_id;
            $sensor->status = $request->status;
            $sensor->location = $request->location;
            $sensor->light_intensity = $request->light_intensity;
            $sensor->save();

            Log::info('Sensor created successfully', ['sensor_id' => $sensor->id]);
            return redirect()->route('admin.sensors.index')->with('success', 'Sensor created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create sensor', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create sensor: ' . $e->getMessage());
        }
    }

    public function showReadings($id)
    {
        $sensor = Sensor::findOrFail($id);
        $readings = $sensor->sensorData()->latest()->paginate(10);
        return view('admin.sensors.readings', compact('sensor', 'readings'));
    }

    public function edit($id)
    {
        $sensor = Sensor::findOrFail($id);
        $farms = Farm::all();
        $plants = Plant::all();
        return view('admin.sensors.edit', compact('sensor', 'farms', 'plants'));
    }

    public function update(Request $request, $id)
    {
        $sensor = Sensor::findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:temperature,humidity,soil_moisture,ph,nutrient',
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'status' => 'required|in:active,inactive,faulty',
                'location' => 'nullable|string|max:255',
                'light_intensity' => 'nullable|numeric',
            ]);

            $sensor->name = $request->name;
            $sensor->type = $request->type;
            $sensor->farm_id = $request->farm_id;
            $sensor->plant_id = $request->plant_id;
            $sensor->status = $request->status;
            $sensor->location = $request->location;
            $sensor->light_intensity = $request->light_intensity;
            $sensor->save();

            Log::info('Sensor updated successfully', ['sensor_id' => $sensor->id]);
            return redirect()->route('admin.sensors.index')->with('success', 'Sensor updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor update', ['sensor_id' => $sensor->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update sensor', ['sensor_id' => $sensor->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update sensor: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $sensor = Sensor::findOrFail($id);
            $sensor->delete();

            Log::info('Sensor deleted successfully', ['sensor_id' => $id]);
            return redirect()->route('admin.sensors.index')->with('success', 'Sensor deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete sensor', ['sensor_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.sensors.index')->with('error', 'Failed to delete sensor: ' . $e->getMessage());
        }
    }
}