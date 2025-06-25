<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\DiseaseDetection;
use App\Models\Plant;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmerManagerDiseaseDetectionController extends Controller
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
        $disease_name = $request->input('disease_name');

        $detectionsQuery = DiseaseDetection::whereHas('plant', function ($query) use ($farm) {
            $query->where('farm_id', $farm->id);
        })->with('plant');

        // البحث
        if ($search) {
            $detectionsQuery->where('disease_name', 'like', "%{$search}%")
                            ->orWhereHas('plant', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            });
        }

        // فلترة حسب النبات
        if ($plant_id) {
            $detectionsQuery->where('plant_id', $plant_id);
        }

        // فلترة حسب اسم المرض
        if ($disease_name) {
            $detectionsQuery->where('disease_name', $disease_name);
        }

        // الترتيب
        if ($sort === 'newest') {
            $detectionsQuery->latest();
        } elseif ($sort === 'oldest') {
            $detectionsQuery->oldest();
        }

        $detections = $detectionsQuery->paginate(10);
        $plants = Plant::where('farm_id', $farm->id)->get(); // جلب النباتات الخاصة بالفارمر
        $diseaseNames = DiseaseDetection::distinct()->pluck('disease_name'); // جلب أسماء الأمراض الفريدة

        return view('farmer_manager.disease-detections.index', compact('detections', 'plants', 'diseaseNames'));
    }

    public function create()
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $plants = Plant::where('farm_id', $farm->id)->get();
        return view('farmer_manager.disease-detections.create', compact('plants'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();

        try {
            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'disease_name' => 'required|string|max:255',
                'confidence' => 'required|numeric|min:0|max:1',
            ]);

            $detection = DiseaseDetection::create([
                'plant_id' => $request->plant_id,
                'disease_name' => $request->disease_name,
                'confidence' => $request->confidence,
            ]);

            Log::info('Disease Detection created by Farmer Manager', ['detection_id' => $detection->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.disease-detections.index')->with('success', 'Disease detection created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for disease detection creation', ['errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create disease detection', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to create disease detection.');
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $detection = DiseaseDetection::whereHas('plant', function ($query) use ($farm) {
            $query->where('farm_id', $farm->id);
        })->findOrFail($id);
        $plants = Plant::where('farm_id', $farm->id)->get();
        return view('farmer_manager.disease-detections.edit', compact('detection', 'plants'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $detection = DiseaseDetection::whereHas('plant', function ($query) use ($farm) {
            $query->where('farm_id', $farm->id);
        })->findOrFail($id);

        try {
            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'disease_name' => 'required|string|max:255',
                'confidence' => 'required|numeric|min:0|max:1',
            ]);

            $detection->update([
                'plant_id' => $request->plant_id,
                'disease_name' => $request->disease_name,
                'confidence' => $request->confidence,
            ]);

            Log::info('Disease Detection updated by Farmer Manager', ['detection_id' => $detection->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.disease-detections.index')->with('success', 'Disease detection updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for disease detection update', ['detection_id' => $detection->id, 'errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update disease detection', ['detection_id' => $detection->id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update disease detection.');
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $detection = DiseaseDetection::whereHas('plant', function ($query) use ($farm) {
            $query->where('farm_id', $farm->id);
        })->findOrFail($id);

        try {
            $detection->delete();
            Log::info('Disease Detection deleted by Farmer Manager', ['detection_id' => $id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.disease-detections.index')->with('success', 'Disease detection deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete disease detection', ['detection_id' => $id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.disease-detections.index')->with('error', 'Failed to delete disease detection.');
        }
    }
}