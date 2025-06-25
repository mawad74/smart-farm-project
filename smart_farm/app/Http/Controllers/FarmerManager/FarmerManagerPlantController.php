<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Farm;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmerManagerPlantController extends Controller
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
        $health_status = $request->input('health_status');

        $plantsQuery = Plant::where('farm_id', $farm->id)->with('farm');

        // البحث
        if ($search) {
            $plantsQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
        }

        // فلترة حسب النوع
        if ($type) {
            $plantsQuery->where('type', $type);
        }

        // فلترة حسب حالة الصحة
        if ($health_status) {
            $plantsQuery->where('health_status', $health_status);
        }

        // الترتيب
        if ($sort === 'newest') {
            $plantsQuery->latest();
        } elseif ($sort === 'oldest') {
            $plantsQuery->oldest();
        }

        $plants = $plantsQuery->paginate(10);
        $farms = Farm::where('user_id', $user->id)->get(); // جلب مزارع الفارمر فقط

        return view('farmer_manager.plants.index', compact('plants', 'farms'));
    }

    public function create()
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        return view('farmer_manager.plants.create', compact('farm'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'health_status' => 'required|in:healthy,diseased,needs_attention',
                'growth_rate' => 'nullable|numeric',
                'yield_prediction' => 'nullable|numeric',
            ]);

            $plant = Plant::create([
                'farm_id' => $farm->id,
                'name' => $request->name,
                'type' => $request->type,
                'health_status' => $request->health_status,
                'growth_rate' => $request->growth_rate,
                'yield_prediction' => $request->yield_prediction,
            ]);

            Log::info('Plant created by Farmer Manager', ['plant_id' => $plant->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.plants.index')->with('success', 'Plant created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for plant creation', ['errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create plant', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to create plant.');
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $plant = Plant::where('farm_id', $farm->id)->findOrFail($id);
        return view('farmer_manager.plants.edit', compact('plant'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $plant = Plant::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'health_status' => 'required|in:healthy,diseased,needs_attention',
                'growth_rate' => 'nullable|numeric',
                'yield_prediction' => 'nullable|numeric',
            ]);

            $plant->update([
                'name' => $request->name,
                'type' => $request->type,
                'health_status' => $request->health_status,
                'growth_rate' => $request->growth_rate,
                'yield_prediction' => $request->yield_prediction,
            ]);

            Log::info('Plant updated by Farmer Manager', ['plant_id' => $plant->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.plants.index')->with('success', 'Plant updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for plant update', ['plant_id' => $plant->id, 'errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update plant', ['plant_id' => $plant->id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update plant.');
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $plant = Plant::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $plant->delete();
            Log::info('Plant deleted by Farmer Manager', ['plant_id' => $id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.plants.index')->with('success', 'Plant deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete plant', ['plant_id' => $id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.plants.index')->with('error', 'Failed to delete plant.');
        }
    }
}