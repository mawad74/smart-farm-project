<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Http\Resources\AlertResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $farm_id = $request->input('farm_id');
            $status = $request->input('status'); // هتكون true أو false
    
            $alertsQuery = Alert::with('farm', 'sensor', 'user');
    
            // Search
            if ($search) {
                $alertsQuery->where('message', 'like', "%{$search}%");
            }
    
            // Filter by farm
            if ($farm_id) {
                $alertsQuery->where('farm_id', $farm_id);
            }
    
            // Filter by status
            if ($status !== null) { // التأكد إن $status مش null
                $alertsQuery->where('status', filter_var($status, FILTER_VALIDATE_BOOLEAN));
            }
    
            // Sort
            if ($sort === 'newest') {
                $alertsQuery->latest();
            } elseif ($sort === 'oldest') {
                $alertsQuery->oldest();
            }
    
            $alerts = $alertsQuery->paginate(10);
    
            return response()->json([
                'status' => 'success',
                'data' => AlertResource::collection($alerts),
                'message' => 'Alerts retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve alerts', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving alerts. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'sensor_id' => 'nullable|exists:sensors,id',
                'user_id' => 'nullable|exists:users,id',
                'message' => 'required|string|max:255',
                'status' => 'required|boolean', // تعديل من in:pending,dismissed,resolved إلى boolean
                'priority' => 'nullable|string|max:50',
                'action_taken' => 'nullable|string|max:255',
                'channel' => 'nullable|in:email,sms,app_notification',
            ]);
    
            $alert = new Alert();
            $alert->farm_id = $request->farm_id;
            $alert->sensor_id = $request->sensor_id;
            $alert->user_id = $request->user_id;
            $alert->message = $request->message;
            $alert->status = $request->status; // القيمة هنا هتكون true أو false
            $alert->priority = $request->priority;
            $alert->action_taken = $request->action_taken;
            $alert->channel = $request->channel;
            $alert->save();
    
            Log::info('Alert created successfully', ['alert_id' => $alert->id]);
            return response()->json([
                'status' => 'success',
                'data' => new AlertResource($alert->load(['farm', 'sensor', 'user'])),
                'message' => 'Alert created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for alert creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create alert', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the alert. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $alert = Alert::with('farm', 'sensor', 'user')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new AlertResource($alert),
                'message' => 'Alert retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve alert', ['alert_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Alert with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $alert = Alert::findOrFail($id);
    
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'sensor_id' => 'nullable|exists:sensors,id',
                'user_id' => 'nullable|exists:users,id',
                'message' => 'required|string|max:255',
                'status' => 'required|boolean', // تعديل من in:pending,dismissed,resolved إلى boolean
                'priority' => 'nullable|string|max:50',
                'action_taken' => 'nullable|string|max:255',
                'channel' => 'nullable|in:email,sms,app_notification',
            ]);
    
            $alert->farm_id = $request->farm_id;
            $alert->sensor_id = $request->sensor_id;
            $alert->user_id = $request->user_id;
            $alert->message = $request->message;
            $alert->status = $request->status; // القيمة هنا هتكون true أو false
            $alert->priority = $request->priority;
            $alert->action_taken = $request->action_taken;
            $alert->channel = $request->channel;
            $alert->save();
    
            Log::info('Alert updated successfully', ['alert_id' => $alert->id]);
            return response()->json([
                'status' => 'success',
                'data' => new AlertResource($alert->load(['farm', 'sensor', 'user'])),
                'message' => 'Alert updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for alert update', ['alert_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update alert', ['alert_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Alert with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $alert = Alert::findOrFail($id);
            $alert->delete();

            Log::info('Alert deleted successfully', ['alert_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Alert with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete alert', ['alert_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Alert with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}