<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Actuator;
use App\Models\Farm;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminActuatorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $farm_id = $request->input('farm_id');
        $plant_id = $request->input('plant_id');
        $type = $request->input('type');
        $status = $request->input('status');

        $actuatorsQuery = Actuator::with('farm', 'plant');

        // البحث
        if ($search) {
            $actuatorsQuery->where('type', 'like', "%{$search}%")
                           ->orWhere('action_type', 'like', "%{$search}%")
                           ->orWhereHas('farm', function ($query) use ($search) {
                               $query->where('name', 'like', "%{$search}%");
                           })
                           ->orWhereHas('plant', function ($query) use ($search) {
                               $query->where('name', 'like', "%{$search}%");
                           });
        }

        // فلترة حسب المزرعة
        if ($farm_id) {
            $actuatorsQuery->where('farm_id', $farm_id);
        }

        // فلترة حسب النبات
        if ($plant_id) {
            $actuatorsQuery->where('plant_id', $plant_id);
        }

        // فلترة حسب النوع
        if ($type) {
            $actuatorsQuery->where('type', $type);
        }

        // فلترة حسب الحالة
        if ($status) {
            $actuatorsQuery->where('status', $status);
        }

        // الترتيب
        if ($sort === 'newest') {
            $actuatorsQuery->latest();
        } elseif ($sort === 'oldest') {
            $actuatorsQuery->oldest();
        }

        $actuators = $actuatorsQuery->paginate(10);
        $farms = Farm::all();
        $plants = Plant::all();

        return view('admin.actuators.index', compact('actuators', 'farms', 'plants'));
    }

    public function create()
    {
        $farms = Farm::all();
        $plants = Plant::all();
        return view('admin.actuators.create', compact('farms', 'plants'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'type' => 'required|in:irrigation_pump,ventilation,lighting',
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'nullable|string|max:255',
                'last_triggered_at' => 'nullable|date',
            ]);

            $actuator = new Actuator();
            $actuator->farm_id = $request->farm_id;
            $actuator->plant_id = $request->plant_id;
            $actuator->type = $request->type;
            $actuator->status = $request->status;
            $actuator->action_type = $request->action_type;
            $actuator->last_triggered_at = $request->last_triggered_at;
            $actuator->save();

            Log::info('Actuator created successfully', ['actuator_id' => $actuator->id]);
            return redirect()->route('admin.actuators.index')->with('success', 'Actuator created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create actuator', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create actuator: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $actuator = Actuator::findOrFail($id);
        $farms = Farm::all();
        $plants = Plant::all();
        return view('admin.actuators.edit', compact('actuator', 'farms', 'plants'));
    }

    public function update(Request $request, $id)
    {
        $actuator = Actuator::findOrFail($id);

        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'type' => 'required|in:irrigation_pump,ventilation,lighting',
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'nullable|string|max:255',
                'last_triggered_at' => 'nullable|date',
            ]);

            $actuator->farm_id = $request->farm_id;
            $actuator->plant_id = $request->plant_id;
            $actuator->type = $request->type;
            $actuator->status = $request->status;
            $actuator->action_type = $request->action_type;
            $actuator->last_triggered_at = $request->last_triggered_at;
            $actuator->save();

            Log::info('Actuator updated successfully', ['actuator_id' => $actuator->id]);
            return redirect()->route('admin.actuators.index')->with('success', 'Actuator updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator update', ['actuator_id' => $actuator->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update actuator', ['actuator_id' => $actuator->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update actuator: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $actuator = Actuator::findOrFail($id);
            $actuator->delete();

            Log::info('Actuator deleted successfully', ['actuator_id' => $id]);
            return redirect()->route('admin.actuators.index')->with('success', 'Actuator deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete actuator', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.actuators.index')->with('error', 'Failed to delete actuator: ' . $e->getMessage());
        }
    }
}