<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\Farm;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmerManagerSensorController extends Controller
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
        $type = $request->input('type');
        $status = $request->input('status');

        $sensorsQuery = Sensor::where('farm_id', $farm->id)->with('farm', 'plant');

        // البحث
        if ($search) {
            $sensorsQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('type', 'like', "%{$search}%");
        }

        // فلترة حسب النوع
        if ($type) {
            $sensorsQuery->where('type', $type);
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
        $farms = Farm::where('user_id', $user->id)->get(); // جلب مزارع الفارمر فقط
        $plants = Plant::where('farm_id', $farm->id)->get(); // جلب النباتات الخاصة بالفارمر

        return view('farmer_manager.sensors.index', compact('sensors', 'farms', 'plants'));
    }

    public function create()
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $plants = Plant::where('farm_id', $farm->id)->get();
        return view('farmer_manager.sensors.create', compact('farm', 'plants'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:temperature,humidity,soil_moisture,ph,nutrient',
                'status' => 'required|in:active,inactive,faulty',
                'location' => 'nullable|string|max:255',
                'light_intensity' => 'nullable|numeric',
                'plant_id' => 'nullable|exists:plants,id',
            ]);

            $sensor = Sensor::create([
                'farm_id' => $farm->id,
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'location' => $request->location,
                'light_intensity' => $request->light_intensity,
                'plant_id' => $request->plant_id,
            ]);

            Log::info('Sensor created by Farmer Manager', ['sensor_id' => $sensor->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.sensors.index')->with('success', 'Sensor created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor creation', ['errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create sensor', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to create sensor.');
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $sensor = Sensor::where('farm_id', $farm->id)->findOrFail($id);
        $plants = Plant::where('farm_id', $farm->id)->get();
        return view('farmer_manager.sensors.edit', compact('sensor', 'plants'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $sensor = Sensor::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:temperature,humidity,soil_moisture,ph,nutrient',
                'status' => 'required|in:active,inactive,faulty',
                'location' => 'nullable|string|max:255',
                'light_intensity' => 'nullable|numeric',
                'plant_id' => 'nullable|exists:plants,id',
            ]);

            $sensor->update([
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'location' => $request->location,
                'light_intensity' => $request->light_intensity,
                'plant_id' => $request->plant_id,
            ]);

            Log::info('Sensor updated by Farmer Manager', ['sensor_id' => $sensor->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.sensors.index')->with('success', 'Sensor updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for sensor update', ['sensor_id' => $sensor->id, 'errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update sensor', ['sensor_id' => $sensor->id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update sensor.');
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $sensor = Sensor::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $sensor->delete();
            Log::info('Sensor deleted by Farmer Manager', ['sensor_id' => $id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.sensors.index')->with('success', 'Sensor deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete sensor', ['sensor_id' => $id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.sensors.index')->with('error', 'Failed to delete sensor.');
        }
    }
}