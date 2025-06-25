<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Http\Resources\AlertResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
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
            $status = $request->input('status');

            $alertsQuery = Alert::where('farm_id', $farm->id)->with('farm', 'sensor', 'user');

            if ($search) {
                $alertsQuery->where('message', 'like', "%{$search}%");
            }

            if ($status !== null) {
                $alertsQuery->where('status', filter_var($status, FILTER_VALIDATE_BOOLEAN));
            }

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
            Log::error('Failed to retrieve alerts for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving alerts.',
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
                'sensor_id' => 'nullable|exists:sensors,id',
                'message' => 'required|string|max:255',
                'status' => 'required|boolean',
                'priority' => 'nullable|in:low,medium,high',
                'action_taken' => 'nullable|string|max:255',
                'channel' => 'nullable|in:email,sms,ui',
            ]);

            $alert = Alert::create([
                'farm_id' => $farm->id,
                'sensor_id' => $request->sensor_id,
                'user_id' => $user->id,
                'message' => $request->message,
                'status' => $request->status,
                'priority' => $request->priority,
                'action_taken' => $request->action_taken,
                'channel' => $request->channel,
            ]);

            Log::info('Alert created by Farmer Manager', ['alert_id' => $alert->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new AlertResource($alert->load(['farm', 'sensor', 'user'])),
                'message' => 'Alert created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for alert creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create alert', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the alert.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $alert = Alert::where('farm_id', $farm->id)->with('farm', 'sensor', 'user')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new AlertResource($alert),
                'message' => 'Alert retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve alert for farmer', ['alert_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Alert with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $alert = Alert::where('farm_id', $farm->id)->findOrFail($id);

            $validated = $request->validate([
                'sensor_id' => 'nullable|exists:sensors,id',
                'message' => 'required|string|max:255',
                'status' => 'required|boolean',
                'priority' => 'nullable|in:low,medium,high',
                'action_taken' => 'nullable|string|max:255',
                'channel' => 'nullable|in:email,sms,ui',
            ]);

            $alert->update($validated);

            Log::info('Alert updated by Farmer Manager', ['alert_id' => $alert->id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new AlertResource($alert->load(['farm', 'sensor', 'user'])),
                'message' => 'Alert updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for alert update', ['alert_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update alert', ['alert_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Alert with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $alert = Alert::where('farm_id', $farm->id)->findOrFail($id);
            $alert->delete();

            Log::info('Alert deleted by Farmer Manager', ['alert_id' => $id, 'farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'message' => "Alert with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete alert', ['alert_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Alert with ID {$id} not found or you do not have access to delete.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}