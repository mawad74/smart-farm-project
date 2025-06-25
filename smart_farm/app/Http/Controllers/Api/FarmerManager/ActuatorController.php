<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Actuator;
use App\Http\Resources\ActuatorResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ActuatorController extends Controller
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
            $type = $request->input('type');
            $status = $request->input('status');

            $actuatorsQuery = Actuator::where('farm_id', $farm->id)->with('farm', 'plant');

            if ($search) {
                $actuatorsQuery->where('type', 'like', "%{$search}%")
                               ->orWhereHas('plant', function ($query) use ($search) {
                                   $query->where('name', 'like', "%{$search}%");
                               });
            }

            if ($plant_id) {
                $actuatorsQuery->where('plant_id', $plant_id);
            }

            if ($type) {
                $actuatorsQuery->where('type', $type);
            }

            if ($status) {
                $actuatorsQuery->where('status', $status);
            }

            if ($sort === 'newest') {
                $actuatorsQuery->latest();
            } elseif ($sort === 'oldest') {
                $actuatorsQuery->oldest();
            }

            $actuators = $actuatorsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => ActuatorResource::collection($actuators),
                'message' => 'Actuators retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve actuators for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving actuators.',
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
                'plant_id' => 'nullable|exists:plants,id',
                'type' => 'required|in:irrigation_pump,ventilation,lighting',
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

            Log::info('Actuator created by Farmer Manager', ['actuator_id' => $actuator->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ActuatorResource($actuator->load(['farm', 'plant'])),
                'message' => 'Actuator created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create actuator', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the actuator.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $actuator = Actuator::where('farm_id', $farm->id)->with('farm', 'plant')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new ActuatorResource($actuator),
                'message' => 'Actuator retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve actuator for farmer', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Actuator with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $actuator = Actuator::where('farm_id', $farm->id)->findOrFail($id);

            $validated = $request->validate([
                'plant_id' => 'nullable|exists:plants,id',
                'type' => 'required|in:irrigation_pump,ventilation,lighting',
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'nullable|string|max:255',
                'last_triggered_at' => 'nullable|date',
            ]);

            $actuator->update($validated);

            Log::info('Actuator updated by Farmer Manager', ['actuator_id' => $actuator->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ActuatorResource($actuator->load(['farm', 'plant'])),
                'message' => 'Actuator updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator update', ['actuator_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update actuator', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Actuator with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $actuator = Actuator::where('farm_id', $farm->id)->findOrFail($id);
            $actuator->delete();

            Log::info('Actuator deleted by Farmer Manager', ['actuator_id' => $id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'message' => "Actuator with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete actuator', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Actuator with ID {$id} not found or you do not have access to delete.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}