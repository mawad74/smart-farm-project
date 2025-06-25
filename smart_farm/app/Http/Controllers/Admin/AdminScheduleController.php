<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Farm;
use App\Models\Plant;
use App\Models\Actuator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminScheduleController extends Controller
{
public function index(Request $request)
{
    $search = $request->input('search');
    $sort = $request->input('sort', 'newest');
    $farm_id = $request->input('farm_id');
    $plant_id = $request->input('plant_id');
    $actuator_id = $request->input('actuator_id');
    $status = $request->input('status');

    $schedulesQuery = Schedule::with('farm', 'plant', 'actuator');

    // البحث
    if ($search) {
        $schedulesQuery->whereHas('farm', function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->orWhereHas('plant', function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->orWhereHas('actuator', function ($query) use ($search) {
            $query->where('type', 'like', "%{$search}%");
        });
    }

    // فلترة حسب المزرعة
    if ($farm_id) {
        $schedulesQuery->where('farm_id', $farm_id);
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
    $farms = Farm::all(); // جلب كل المزارع للفلترة
    $plants = Plant::all(); // جلب كل النباتات للفلترة
    $actuators = Actuator::all(); // جلب كل الأجهزة للفلترة

    return view('admin.schedules.index', compact('schedules', 'farms', 'plants', 'actuators'));
}

    public function create()
    {
        $farms = Farm::all();
        $plants = Plant::all();
        $actuators = Actuator::all();
        return view('admin.schedules.create', compact('farms', 'plants', 'actuators'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'actuator_id' => 'required|exists:actuators,id',
                'schedule_time' => 'required|date',
                'status' => 'required|in:pending,completed',
                'weather_forecast_integration' => 'required|boolean',
                'priority_zone' => 'nullable|integer',
            ]);

            $schedule = new Schedule();
            $schedule->farm_id = $request->farm_id;
            $schedule->plant_id = $request->plant_id;
            $schedule->actuator_id = $request->actuator_id;
            $schedule->schedule_time = $request->schedule_time;
            $schedule->status = $request->status;
            $schedule->weather_forecast_integration = $request->weather_forecast_integration;
            $schedule->priority_zone = $request->priority_zone;
            $schedule->save();

            Log::info('Schedule created successfully', ['schedule_id' => $schedule->id]);
            return redirect()->route('admin.schedules.index')->with('success', 'Schedule created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create schedule', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create schedule: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        $farms = Farm::all();
        $plants = Plant::all();
        $actuators = Actuator::all();
        return view('admin.schedules.edit', compact('schedule', 'farms', 'plants', 'actuators'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'actuator_id' => 'required|exists:actuators,id',
                'schedule_time' => 'required|date',
                'status' => 'required|in:pending,completed',
                'weather_forecast_integration' => 'required|boolean',
                'priority_zone' => 'nullable|integer',
            ]);

            $schedule->farm_id = $request->farm_id;
            $schedule->plant_id = $request->plant_id;
            $schedule->actuator_id = $request->actuator_id;
            $schedule->schedule_time = $request->schedule_time;
            $schedule->status = $request->status;
            $schedule->weather_forecast_integration = $request->weather_forecast_integration;
            $schedule->priority_zone = $request->priority_zone;
            $schedule->save();

            Log::info('Schedule updated successfully', ['schedule_id' => $schedule->id]);
            return redirect()->route('admin.schedules.index')->with('success', 'Schedule updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule update', ['schedule_id' => $schedule->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update schedule', ['schedule_id' => $schedule->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();

            Log::info('Schedule deleted successfully', ['schedule_id' => $id]);
            return redirect()->route('admin.schedules.index')->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete schedule', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.schedules.index')->with('error', 'Failed to delete schedule: ' . $e->getMessage());
        }
    }
}