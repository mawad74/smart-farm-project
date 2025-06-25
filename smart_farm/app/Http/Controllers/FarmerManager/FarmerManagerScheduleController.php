<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Farm;
use App\Models\Plant;
use App\Models\Actuator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmerManagerScheduleController extends Controller
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
        $plant_id = $request->input('plant_id');
        $actuator_id = $request->input('actuator_id');
        $status = $request->input('status');

        $schedulesQuery = Schedule::where('farm_id', $farm->id)->with('farm', 'plant', 'actuator');

        // البحث
        if ($search) {
            $schedulesQuery->where('schedule_time', 'like', "%{$search}%")
                           ->orWhereHas('plant', function ($query) use ($search) {
                               $query->where('name', 'like', "%{$search}%");
                           })
                           ->orWhereHas('actuator', function ($query) use ($search) {
                               $query->where('type', 'like', "%{$search}%");
                           });
        }

        // فلترة حسب النبات
        if ($plant_id) {
            $schedulesQuery->where('plant_id', $plant_id);
        }

        // فلترة حسب الجهاز
        if ($actuator_id) {
            $schedulesQuery->where('actuator_id', $actuator_id);
        }

        // فلترة حسب الحالة
        if ($status) {
            $schedulesQuery->where('status', $status);
        }

        // الترتيب
        if ($sort === 'newest') {
            $schedulesQuery->latest();
        } elseif ($sort === 'oldest') {
            $schedulesQuery->oldest();
        }

        $schedules = $schedulesQuery->paginate(10);
        $farms = Farm::where('user_id', $user->id)->get(); // جلب مزارع الفارمر فقط
        $plants = Plant::where('farm_id', $farm->id)->get(); // جلب النباتات الخاصة بالفارمر
        $actuators = Actuator::all(); // جلب كل الأجهزة (يمكن نحددها للفارمر لاحقًا)

        return view('farmer_manager.schedules.index', compact('schedules', 'farms', 'plants', 'actuators'));
    }

    public function create()
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $plants = Plant::where('farm_id', $farm->id)->get();
        $actuators = Actuator::all();
        return view('farmer_manager.schedules.create', compact('farm', 'plants', 'actuators'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();

        try {
            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'actuator_id' => 'required|exists:actuators,id',
                'schedule_time' => 'required|date',
                'status' => 'required|in:pending,completed',
                'weather_forecast_integration' => 'boolean',
                'priority_zone' => 'nullable|string|max:255',
            ]);

            $schedule = Schedule::create([
                'farm_id' => $farm->id,
                'plant_id' => $request->plant_id,
                'actuator_id' => $request->actuator_id,
                'schedule_time' => $request->schedule_time,
                'status' => $request->status,
                'weather_forecast_integration' => $request->weather_forecast_integration ?? false,
                'priority_zone' => $request->priority_zone,
            ]);

            Log::info('Schedule created by Farmer Manager', ['schedule_id' => $schedule->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.schedules.index')->with('success', 'Schedule created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule creation', ['errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create schedule', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to create schedule.');
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $schedule = Schedule::where('farm_id', $farm->id)->findOrFail($id);
        $plants = Plant::where('farm_id', $farm->id)->get();
        $actuators = Actuator::all();
        return view('farmer_manager.schedules.edit', compact('schedule', 'plants', 'actuators'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $schedule = Schedule::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'actuator_id' => 'required|exists:actuators,id',
                'schedule_time' => 'required|date',
                'status' => 'required|in:pending,completed',
                'weather_forecast_integration' => 'boolean',
                'priority_zone' => 'nullable|string|max:255',
            ]);

            $schedule->update([
                'plant_id' => $request->plant_id,
                'actuator_id' => $request->actuator_id,
                'schedule_time' => $request->schedule_time,
                'status' => $request->status,
                'weather_forecast_integration' => $request->weather_forecast_integration ?? false,
                'priority_zone' => $request->priority_zone,
            ]);

            Log::info('Schedule updated by Farmer Manager', ['schedule_id' => $schedule->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.schedules.index')->with('success', 'Schedule updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule update', ['schedule_id' => $schedule->id, 'errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update schedule', ['schedule_id' => $schedule->id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update schedule.');
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $schedule = Schedule::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $schedule->delete();
            Log::info('Schedule deleted by Farmer Manager', ['schedule_id' => $id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.schedules.index')->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete schedule', ['schedule_id' => $id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.schedules.index')->with('error', 'Failed to delete schedule.');
        }
    }
}