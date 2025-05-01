<?php

namespace App\Http\Controllers\Api\Shared;

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

            // Search
            if ($search) {
                $detectionsQuery->where('disease_name', 'like', "%{$search}%");
            }

            // Filter by plant
            if ($plant_id) {
                $detectionsQuery->where('plant_id', $plant_id);
            }

            // Filter by disease name
            if ($disease_name) {
                $detectionsQuery->where('disease_name', $disease_name);
            }

            // Sort
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
                'message' => 'An unexpected error occurred while retrieving disease detections. Please try again later.',
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

            $detection = new DiseaseDetection();
            $detection->plant_id = $request->plant_id;
            $detection->disease_name = $request->disease_name;
            $detection->confidence = $request->confidence;
            $detection->action_taken = $request->action_taken;
            $detection->save();

            Log::info('Disease Detection created successfully', ['detection_id' => $detection->id]);
            return response()->json([
                'status' => 'success',
                'data' => new DiseaseDetectionResource($detection->load('plant')),
                'message' => 'Disease Detection created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for disease detection creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create disease detection', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the disease detection. Please try again later.',
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
                'message' => "Disease Detection with ID {$id} not found. Please check the ID and try again.",
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

            $detection->plant_id = $request->plant_id;
            $detection->disease_name = $request->disease_name;
            $detection->confidence = $request->confidence;
            $detection->action_taken = $request->action_taken;
            $detection->save();

            Log::info('Disease Detection updated successfully', ['detection_id' => $detection->id]);
            return response()->json([
                'status' => 'success',
                'data' => new DiseaseDetectionResource($detection->load('plant')),
                'message' => 'Disease Detection updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for disease detection update', ['detection_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update disease detection', ['detection_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Disease Detection with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $detection = DiseaseDetection::findOrFail($id);
            $detection->delete();

            Log::info('Disease Detection deleted successfully', ['detection_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Disease Detection with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete disease detection', ['detection_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Disease Detection with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}