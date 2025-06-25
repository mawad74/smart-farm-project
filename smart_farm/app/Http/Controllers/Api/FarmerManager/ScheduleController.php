<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Http\Resources\ScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
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
            $actuator_id = $request->input('actuator_id');
            $status = $request->input('status');

            $schedulesQuery = Schedule::where('farm_id', $farm->id)->with('farm', 'plant', 'actuator');

            if ($search) {
                $schedulesQuery->whereHas('plant', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('actuator', function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%");
                });
            }

            if ($plant_id) {
                $schedulesQuery->where('plant_id', $plant_id);
            }

            if ($actuator_id) {
                $schedulesQuery->where('actuator_id', $actuator_id);
            }

            if ($status) {
                $schedulesQuery->where('status', $status);
            }

            if ($sort === 'newest') {
                $schedulesQuery->latest();
            } elseif ($sort === 'oldest') {
                $schedulesQuery->oldest();
            }

            $schedules = $schedulesQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => ScheduleResource::collection($schedules),
                'message' => 'Schedules retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve schedules for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving schedules.',
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
                'actuator_id' => 'required|exists:actuators,id',
                'schedule_time' => 'required|date',
                'status' => 'required|in:pending,completed',
                'weather_forecast_integration' => 'required|boolean',
                'priority_zone' => 'nullable|integer',
            ]);

            $schedule = Schedule::create([
                'farm_id' => $farm->id,
                'plant_id' => $request->plant_id,
                'actuator_id' => $request->actuator_id,
                'schedule_time' => $request->schedule_time,
                'status' => $request->status,
                'weather_forecast_integration' => $request->weather_forecast_integration,
                'priority_zone' => $request->priority_zone,
            ]);

            Log::info('Schedule created by Farmer Manager', ['schedule_id' => $schedule->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ScheduleResource($schedule->load(['farm', 'plant', 'actuator'])),
                'message' => 'Schedule created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create schedule', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the schedule.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $schedule = Schedule::where('farm_id', $farm->id)->with('farm', 'plant', 'actuator')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new ScheduleResource($schedule),
                'message' => 'Schedule retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve schedule for farmer', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Schedule with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $schedule = Schedule::where('farm_id', $farm->id)->findOrFail($id);

            $validated = $request->validate([
                'plant_id' => 'required|exists:plants,id',
                'actuator_id' => 'required|exists:actuators,id',
                'schedule_time' => 'required|date',
                'status' => 'required|in:pending,completed',
                'weather_forecast_integration' => 'required|boolean',
                'priority_zone' => 'nullable|integer',
            ]);

            $schedule->update($validated);

            Log::info('Schedule updated by Farmer Manager', ['schedule_id' => $schedule->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ScheduleResource($schedule->load(['farm', 'plant', 'actuator'])),
                'message' => 'Schedule updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule update', ['schedule_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update schedule', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Schedule with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $schedule = Schedule::where('farm_id', $farm->id)->findOrFail($id);
            $schedule->delete();

            Log::info('Schedule deleted by Farmer Manager', ['schedule_id' => $id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'message' => "Schedule with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete schedule', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Schedule with ID {$id} not found or you do not have access to delete.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}