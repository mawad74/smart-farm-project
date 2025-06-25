<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportRequest;
use App\Http\Resources\ReportRequestResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ReportRequestController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $status = $request->input('status');

            $requestsQuery = ReportRequest::with('user', 'farm');

            if ($search) {
                $requestsQuery->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('farm', function ($query) use ($search) {
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
            Log::error('Failed to retrieve report requests', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving report requests.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $requestRecord = ReportRequest::findOrFail($id);
            $requestRecord->update(['status' => 'approved']);

            Log::info('Report request approved', ['request_id' => $id]);
            return response()->json([
                'status' => 'success',
                'data' => new ReportRequestResource($requestRecord),
                'message' => 'Report request approved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to approve report request', ['request_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Report request with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $requestRecord = ReportRequest::findOrFail($id);
            $requestRecord->update(['status' => 'rejected']);

            Log::info('Report request rejected', ['request_id' => $id]);
            return response()->json([
                'status' => 'success',
                'data' => new ReportRequestResource($requestRecord),
                'message' => 'Report request rejected successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to reject report request', ['request_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Report request with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}