<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Http\Resources\PlantResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class PlantController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $type = $request->input('type');
            $farm_id = $request->input('farm_id');
            $health_status = $request->input('health_status');

            $plantsQuery = Plant::with('farm');

            // Search
            if ($search) {
                $plantsQuery->where('name', 'like', "%{$search}%");
            }

            // Filter by type
            if ($type) {
                $plantsQuery->where('type', $type);
            }

            // Filter by farm
            if ($farm_id) {
                $plantsQuery->where('farm_id', $farm_id);
            }

            // Filter by health status
            if ($health_status) {
                $plantsQuery->where('health_status', $health_status);
            }

            // Sort
            if ($sort === 'newest') {
                $plantsQuery->latest();
            } elseif ($sort === 'oldest') {
                $plantsQuery->oldest();
            }

            $plants = $plantsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => PlantResource::collection($plants),
                'message' => 'Plants retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve plants', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving plants. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
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
            return response()->json([
                'status' => 'success',
                'data' => new PlantResource($plant->load('farm')),
                'message' => 'Plant created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for plant creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create plant', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the plant. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $plant = Plant::with('farm')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new PlantResource($plant),
                'message' => 'Plant retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve plant', ['plant_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Plant with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $plant = Plant::findOrFail($id);

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
            return response()->json([
                'status' => 'success',
                'data' => new PlantResource($plant->load('farm')),
                'message' => 'Plant updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for plant update', ['plant_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update plant', ['plant_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Plant with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $plant = Plant::findOrFail($id);
            $plant->delete();

            Log::info('Plant deleted successfully', ['plant_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Plant with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete plant', ['plant_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Plant with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}