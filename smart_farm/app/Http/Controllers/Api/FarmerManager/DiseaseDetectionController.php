<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\DiseaseDetection;
use App\Http\Resources\DiseaseDetectionResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DiseaseDetectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_farmer_manager');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();

            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $plant_id = $request->input('plant_id');
            $disease_name = $request->input('disease_name');

            $detectionsQuery = DiseaseDetection::whereHas('plant', function ($query) use ($farm) {
                $query->where('farm_id', $farm->id);
            })->with('plant');

            if ($search) {
                $detectionsQuery->where('disease_name', 'like', "%{$search}%")
                                ->orWhereHas('plant', function ($query) use ($search) {
                                    $query->where('name', 'like', "%{$search}%");
                                });
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
            Log::error('Failed to retrieve disease detections for farmer', ['error' => $e->getMessage()]);
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
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();

            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'disease_name' => 'required|string|max:255',
                'confidence' => 'required|numeric|between:0,1',
                'action_taken' => 'nullable|string|max:255',
            ]);

            $detection = DiseaseDetection::create($validated);

            Log::info('Disease Detection created by Farmer Manager', ['detection_id' => $detection->id, 'farm_id' => $farm->id]);
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
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $detection = DiseaseDetection::whereHas('plant', function ($query) use ($farm) {
                $query->where('farm_id', $farm->id);
            })->with('plant')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new DiseaseDetectionResource($detection),
                'message' => 'Disease Detection retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve disease detection for farmer', ['detection_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Disease Detection with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $detection = DiseaseDetection::whereHas('plant', function ($query) use ($farm) {
                $query->where('farm_id', $farm->id);
            })->findOrFail($id);

            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'disease_name' => 'required|string|max:255',
                'confidence' => 'required|numeric|between:0,1',
                'action_taken' => 'nullable|string|max:255',
            ]);

            $detection->update($validated);

            Log::info('Disease Detection updated by Farmer Manager', ['detection_id' => $detection->id, 'farm_id' => $farm->id]);
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
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $detection = DiseaseDetection::whereHas('plant', function ($query) use ($farm) {
                $query->where('farm_id', $farm->id);
            })->findOrFail($id);
            $detection->delete();

            Log::info('Disease Detection deleted by Farmer Manager', ['detection_id' => $id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'message' => "Disease Detection with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete disease detection', ['detection_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Disease Detection with ID {$id} not found or you do not have access to delete.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}