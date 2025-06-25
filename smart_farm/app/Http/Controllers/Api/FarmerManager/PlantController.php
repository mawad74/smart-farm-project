<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use App\Models\Farm;
use App\Http\Resources\PlantResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PlantController extends Controller
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
            $type = $request->input('type');
            $health_status = $request->input('health_status');

            $plantsQuery = Plant::where('farm_id', $farm->id)->with('farm');

            if ($search) {
                $plantsQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%");
            }

            if ($type) {
                $plantsQuery->where('type', $type);
            }

            if ($health_status) {
                $plantsQuery->where('health_status', $health_status);
            }

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
            Log::error('Failed to retrieve plants for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving plants.',
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

            Log::info('Plant created by Farmer Manager', ['plant_id' => $plant->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new PlantResource($plant->load('farm')),
                'message' => 'Plant created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for plant creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create plant', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the plant.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $plant = Plant::where('farm_id', $farm->id)->with('farm')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new PlantResource($plant),
                'message' => 'Plant retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve plant for farmer', ['plant_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Plant with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $plant = Plant::where('farm_id', $farm->id)->findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'health_status' => 'required|in:healthy,diseased,needs_attention',
                'growth_rate' => 'nullable|numeric',
                'yield_prediction' => 'nullable|numeric',
            ]);

            $plant->update($validated);

            Log::info('Plant updated by Farmer Manager', ['plant_id' => $plant->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new PlantResource($plant->load('farm')),
                'message' => 'Plant updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for plant update', ['plant_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update plant', ['plant_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Plant with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $plant = Plant::where('farm_id', $farm->id)->findOrFail($id);
            $plant->delete();

            Log::info('Plant deleted by Farmer Manager', ['plant_id' => $id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'message' => "Plant with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete plant', ['plant_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Plant with ID {$id} not found or you do not have access to delete.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}