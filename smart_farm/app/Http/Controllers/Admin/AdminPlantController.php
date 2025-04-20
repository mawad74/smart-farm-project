<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminPlantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $type = $request->input('type');
        $farm_id = $request->input('farm_id');
        $health_status = $request->input('health_status');

        $plantsQuery = Plant::with('farm');

        // البحث
        if ($search) {
            $plantsQuery->where('name', 'like', "%{$search}%");
        }

        // فلترة حسب النوع
        if ($type) {
            $plantsQuery->where('type', $type);
        }

        // فلترة حسب المزرعة
        if ($farm_id) {
            $plantsQuery->where('farm_id', $farm_id);
        }

        // فلترة حسب الحالة الصحية
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
        $farms = Farm::all(); // جلب كل المزارع للفلترة
        return view('admin.plants.index', compact('plants', 'farms'));
    }

    public function create()
    {
        $farms = Farm::all();
        return view('admin.plants.create', compact('farms'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'farm_id' => 'required|exists:farms,id',
                'health_status' => 'required|in:healthy,diseased,needs_attention',
                'growth_rate' => 'nullable|numeric',
                'yield_prediction' => 'nullable|numeric',
            ]);

            $plant = new Plant();
            $plant->name = $request->name;
            $plant->type = $request->type;
            $plant->farm_id = $request->farm_id;
            $plant->health_status = $request->health_status;
            $plant->growth_rate = $request->growth_rate;
            $plant->yield_prediction = $request->yield_prediction;
            $plant->save();

            Log::info('Plant created successfully', ['plant_id' => $plant->id]);
            return redirect()->route('admin.plants.index')->with('success', 'Plant created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for plant creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create plant', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create plant: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $plant = Plant::findOrFail($id);
        $farms = Farm::all();
        return view('admin.plants.edit', compact('plant', 'farms'));
    }

    public function update(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'farm_id' => 'required|exists:farms,id',
                'health_status' => 'required|in:healthy,diseased,needs_attention',
                'growth_rate' => 'nullable|numeric',
                'yield_prediction' => 'nullable|numeric',
            ]);

            $plant->name = $request->name;
            $plant->type = $request->type;
            $plant->farm_id = $request->farm_id;
            $plant->health_status = $request->health_status;
            $plant->growth_rate = $request->growth_rate;
            $plant->yield_prediction = $request->yield_prediction;
            $plant->save();

            Log::info('Plant updated successfully', ['plant_id' => $plant->id]);
            return redirect()->route('admin.plants.index')->with('success', 'Plant updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for plant update', ['plant_id' => $plant->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update plant', ['plant_id' => $plant->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update plant: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $plant = Plant::findOrFail($id);
            $plant->delete();

            Log::info('Plant deleted successfully', ['plant_id' => $id]);
            return redirect()->route('admin.plants.index')->with('success', 'Plant deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete plant', ['plant_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.plants.index')->with('error', 'Failed to delete plant: ' . $e->getMessage());
        }
    }
}