<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Http\Resources\ReportResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
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

            $reportsQuery = Report::where('farm_id', $farm->id)->with('user', 'reportDetails');

            if ($search) {
                $reportsQuery->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            }

            if ($type) {
                $reportsQuery->where('type', $type);
            }

            if ($sort === 'newest') {
                $reportsQuery->latest();
            } elseif ($sort === 'oldest') {
                $reportsQuery->oldest();
            }

            $reports = $reportsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => ReportResource::collection($reports),
                'message' => 'Reports retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve reports for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving reports.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $report = Report::where('farm_id', $farm->id)->with('reportDetails')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => new ReportResource($report),
                'message' => 'Report retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve report for farmer', ['report_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Report with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function exportToPDF($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $report = Report::where('farm_id', $farm->id)->with('reportDetails')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'report_id' => $report->id,
                    'type' => $report->type,
                    'generated_at' => $report->created_at->toDateTimeString(),
                    'details' => $report->reportDetails->map(function ($detail) {
                        return [
                            'category' => $detail->category,
                            'value' => $detail->value,
                            'description' => $detail->description,
                        ];
                    }),
                ],
                'message' => 'Report data prepared for export.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to export report to PDF for farmer', ['report_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Report with ID {$id} not found or an unexpected error occurred while exporting.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}