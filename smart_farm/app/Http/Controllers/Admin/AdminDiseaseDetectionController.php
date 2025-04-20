<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiseaseDetection;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminDiseaseDetectionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $plant_id = $request->input('plant_id');
        $disease_name = $request->input('disease_name');

        $detectionsQuery = DiseaseDetection::with('plant');

        // البحث
        if ($search) {
            $detectionsQuery->where('disease_name', 'like', "%{$search}%");
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
        $plants = Plant::all(); // جلب كل النباتات للفلترة
        $diseaseNames = DiseaseDetection::select('disease_name')->distinct()->pluck('disease_name'); // جلب أسماء الأمراض المميزة للفلترة

        return view('admin.disease-detections.index', compact('detections', 'plants', 'diseaseNames'));
    }

    public function create()
    {
        $plants = Plant::all();
        return view('admin.disease-detections.create', compact('plants'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'disease_name' => 'required|string|max:255',
                'confidence' => 'required|numeric|between:0,1',
            ]);

            $detection = new DiseaseDetection();
            $detection->plant_id = $request->plant_id;
            $detection->disease_name = $request->disease_name;
            $detection->confidence = $request->confidence;
            $detection->save();

            Log::info('Disease Detection created successfully', ['detection_id' => $detection->id]);
            return redirect()->route('admin.disease-detections.index')->with('success', 'Disease Detection created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for disease detection creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create disease detection', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create disease detection: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $detection = DiseaseDetection::findOrFail($id);
        $plants = Plant::all();
        return view('admin.disease-detections.edit', compact('detection', 'plants'));
    }

    public function update(Request $request, $id)
    {
        $detection = DiseaseDetection::findOrFail($id);

        try {
            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'disease_name' => 'required|string|max:255',
                'confidence' => 'required|numeric|between:0,1',
            ]);

            $detection->plant_id = $request->plant_id;
            $detection->disease_name = $request->disease_name;
            $detection->confidence = $request->confidence;
            $detection->save();

            Log::info('Disease Detection updated successfully', ['detection_id' => $detection->id]);
            return redirect()->route('admin.disease-detections.index')->with('success', 'Disease Detection updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for disease detection update', ['detection_id' => $detection->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update disease detection', ['detection_id' => $detection->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update disease detection: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $detection = DiseaseDetection::findOrFail($id);
            $detection->delete();

            Log::info('Disease Detection deleted successfully', ['detection_id' => $id]);
            return redirect()->route('admin.disease-detections.index')->with('success', 'Disease Detection deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete disease detection', ['detection_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.disease-detections.index')->with('error', 'Failed to delete disease detection: ' . $e->getMessage());
        }
    }
}