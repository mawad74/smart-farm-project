<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Actuator;
use App\Models\Farm;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmerManagerActuatorController extends Controller
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
        $farm_id = $request->input('farm_id');
        $plant_id = $request->input('plant_id');
        $type = $request->input('type');
        $status = $request->input('status');

        $actuatorsQuery = Actuator::where('farm_id', $farm->id)->with('farm', 'plant');

        // البحث
        if ($search) {
            $actuatorsQuery->where('type', 'like', "%{$search}%")
                           ->orWhereHas('plant', function ($query) use ($search) {
                               $query->where('name', 'like', "%{$search}%");
                           });
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
        $farms = Farm::where('user_id', $user->id)->get(); // جلب مزارع الفارمر فقط
        $plants = Plant::where('farm_id', $farm->id)->get(); // جلب النباتات الخاصة بالفارمر

        return view('farmer_manager.actuators.index', compact('actuators', 'farms', 'plants'));
    }

    public function create()
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $plants = Plant::where('farm_id', $farm->id)->get();
        return view('farmer_manager.actuators.create', compact('farm', 'plants'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();

        try {
            $validated = $request->validate([
                'plant_id' => 'nullable|exists:plants,id',
                'type' => 'required|string|in:irrigation_pump,ventilation,lighting',
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'nullable|string|max:255',
                'last_triggered_at' => 'nullable|date',
            ]);

            $actuator = Actuator::create([
                'farm_id' => $farm->id,
                'plant_id' => $request->plant_id,
                'type' => $request->type,
                'status' => $request->status,
                'action_type' => $request->action_type,
                'last_triggered_at' => $request->last_triggered_at,
            ]);

            Log::info('Actuator created by Farmer Manager', ['actuator_id' => $actuator->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.actuators.index')->with('success', 'Actuator created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator creation', ['errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create actuator', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to create actuator.');
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $actuator = Actuator::where('farm_id', $farm->id)->findOrFail($id);
        $plants = Plant::where('farm_id', $farm->id)->get();
        return view('farmer_manager.actuators.edit', compact('actuator', 'plants'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $actuator = Actuator::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $validated = $request->validate([
                'plant_id' => 'nullable|exists:plants,id',
                'type' => 'required|string|in:irrigation_pump,ventilation,lighting',
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'nullable|string|max:255',
                'last_triggered_at' => 'nullable|date',
            ]);

            $actuator->update([
                'plant_id' => $request->plant_id,
                'type' => $request->type,
                'status' => $request->status,
                'action_type' => $request->action_type,
                'last_triggered_at' => $request->last_triggered_at,
            ]);

            Log::info('Actuator updated by Farmer Manager', ['actuator_id' => $actuator->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.actuators.index')->with('success', 'Actuator updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator update', ['actuator_id' => $actuator->id, 'errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update actuator', ['actuator_id' => $actuator->id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update actuator.');
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $actuator = Actuator::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $actuator->delete();
            Log::info('Actuator deleted by Farmer Manager', ['actuator_id' => $id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.actuators.index')->with('success', 'Actuator deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete actuator', ['actuator_id' => $id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.actuators.index')->with('error', 'Failed to delete actuator.');
        }
    }
}