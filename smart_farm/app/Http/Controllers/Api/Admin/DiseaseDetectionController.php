<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiseaseDetection;
use App\Http\Resources\DiseaseDetectionResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class DiseaseDetectionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $plant_id = $request->input('plant_id');
            $disease_name = $request->input('disease_name');

            $detectionsQuery = DiseaseDetection::with('plant');

            if ($search) {
                $detectionsQuery->where('disease_name', 'like', "%{$search}%");
            }

            if ($plant_id) {
                $detectionsQuery->where('plant_id', $plant_id);
            }

            if ($disease_name) {
                $detectionsQuery->where('disease_name', $disease_name);
            }

            if ($sort === 'newest') {
                $detectionsQuery->latest();
            } elseif ($sort === 'oldest') {
                $detectionsQuery->oldest();
            }

            $detections = $detectionsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => DiseaseDetectionResource::collection($detections),
                'message' => 'Disease Detections retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve disease detections', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving disease detections.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'disease_name' => 'required|string|max:255',
                'confidence' => 'required|numeric|between:0,1',
                'action_taken' => 'nullable|string|max:255',
            ]);

            $detection = DiseaseDetection::create($validated);

            Log::info('Disease Detection created by Admin', ['detection_id' => $detection->id]);
            return response()->json([
                'status' => 'success',
                'data' => new DiseaseDetectionResource($detection->load('plant')),
                'message' => 'Disease Detection created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for disease detection creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create disease detection', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the disease detection.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $detection = DiseaseDetection::with('plant')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new DiseaseDetectionResource($detection),
                'message' => 'Disease Detection retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve disease detection', ['detection_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Disease Detection with ID {$id} not found.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $detection = DiseaseDetection::findOrFail($id);

            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'disease_name' => 'required|string|max:255',
                'confidence' => 'required|numeric|between:0,1',
                'action_taken' => 'nullable|string|max:255',
            ]);

            $detection->update($validated);

            Log::info('Disease Detection updated by Admin', ['detection_id' => $detection->id]);
            return response()->json([
                'status' => 'success',
                'data' => new DiseaseDetectionResource($detection->load('plant')),
                'message' => 'Disease Detection updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for disease detection update', ['detection_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update disease detection', ['detection_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Disease Detection with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $detection = DiseaseDetection::findOrFail($id);
            $detection->delete();

            Log::info('Disease Detection deleted by Admin', ['detection_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Disease Detection with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete disease detection', ['detection_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Disease Detection with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}