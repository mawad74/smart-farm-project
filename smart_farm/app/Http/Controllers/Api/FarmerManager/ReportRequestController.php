<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\ReportRequest;
use App\Http\Resources\ReportRequestResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReportRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_farmer_manager');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $status = $request->input('status');

            $requestsQuery = ReportRequest::where('user_id', $user->id)->with('farm');

            if ($search) {
                $requestsQuery->whereHas('farm', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            }

            if ($status) {
                $requestsQuery->where('status', $status);
            }

            if ($sort === 'newest') {
                $requestsQuery->latest();
            } elseif ($sort === 'oldest') {
                $requestsQuery->oldest();
            }

            $requests = $requestsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => ReportRequestResource::collection($requests),
                'message' => 'Report requests retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve report requests for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving report requests.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            $validated = $request->validate([
                'type' => 'required|in:crop_health,resource_usage,environmental_conditions,alert_history,system_performance',
                'farm_id' => 'required|exists:farms,id',
            ]);

            $reportRequest = ReportRequest::create([
                'user_id' => $user->id,
                'farm_id' => $request->farm_id,
                'type' => $request->type,
                'status' => 'pending',
            ]);

            Log::info('Report request created by Farmer Manager', ['request_id' => $reportRequest->id, 'user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ReportRequestResource($reportRequest),
                'message' => 'Report request submitted successfully and is awaiting approval.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for report request creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create report request', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while submitting the report request.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}