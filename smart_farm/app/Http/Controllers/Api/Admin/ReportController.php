<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\Sensor;
use App\Models\WeatherData;
use App\Models\Log;
use App\Http\Resources\ReportResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log as Logger;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $type = $request->input('type');

            $reportsQuery = Report::with('user', 'farm', 'reportDetails');

            // Search
            if ($search) {
                $reportsQuery->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('farm', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            }

            // Filter by type
            if ($type) {
                $reportsQuery->where('type', $type);
            }

            // Sort
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
            Logger::error('Failed to retrieve reports', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving reports. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:crop_health,resource_usage,environmental_conditions,alert_history,system_performance',
                'user_id' => 'required|exists:users,id',
                'farm_id' => 'required|exists:farms,id',
            ]);

            // إنشاء التقرير الأساسي بدون تعيين generated_at
            $report = new Report();
            $report->type = $request->type;
            $report->user_id = $request->user_id;
            $report->farm_id = $request->farm_id;
            $report->save();

            // تحديد البيانات بناءً على نوع التقرير (تلقائيًا) وتخزينها في report_details
            if ($request->type === 'system_performance') {
                $activeSensors = Sensor::where('status', 'active')->where('farm_id', $request->farm_id)->count();
                $faultySensors = Sensor::where('status', 'faulty')->where('farm_id', $request->farm_id)->count();
                $communicationErrors = Sensor::where('status', 'faulty')->where('farm_id', $request->farm_id)->count();

                ReportDetail::create(['report_id' => $report->id, 'category' => 'uptime', 'value' => 99.9, 'description' => 'System uptime percentage']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'active_sensors', 'value' => $activeSensors, 'description' => 'Number of active sensors']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'faulty_sensors', 'value' => $faultySensors, 'description' => 'Number of faulty sensors']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'communication_errors', 'value' => $communicationErrors, 'description' => 'Number of communication errors']);
            } elseif ($request->type === 'alert_history') {
                $logs = Log::where('farm_id', $request->farm_id)
                           ->where('status', 'failed')
                           ->orderBy('timestamp', 'desc')
                           ->take(5)
                           ->get();

                foreach ($logs as $log) {
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'alert', 'value' => 0, 'description' => "Alert: {$log->action} - {$log->message} at {$log->timestamp}"]);
                }
            } elseif ($request->type === 'environmental_conditions') {
                $weatherData = WeatherData::where('farm_id', $request->farm_id)
                                          ->orderBy('timestamp', 'desc')
                                          ->take(10)
                                          ->get();
                $averageTemperature = $weatherData->avg('temperature') ?? 0;
                $averageRainfall = $weatherData->avg('rainfall') ?? 0;
                $averageWindSpeed = $weatherData->avg('wind_speed') ?? 0;

                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_temperature', 'value' => $averageTemperature, 'description' => 'Average temperature (°C)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_rainfall', 'value' => $averageRainfall, 'description' => 'Average rainfall (mm)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_wind_speed', 'value' => $averageWindSpeed, 'description' => 'Average wind speed (km/h)']);
            } elseif ($request->type === 'resource_usage') {
                $waterUsage = 1000;
                $electricityUsage = 500;

                ReportDetail::create(['report_id' => $report->id, 'category' => 'water_usage', 'value' => $waterUsage, 'description' => 'Water usage (liters)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'electricity_usage', 'value' => $electricityUsage, 'description' => 'Electricity usage (kWh)']);
            } elseif ($request->type === 'crop_health') {
                $healthyCrops = 80;
                $diseasedCrops = 20;

                ReportDetail::create(['report_id' => $report->id, 'category' => 'healthy_crops', 'value' => $healthyCrops, 'description' => 'Percentage of healthy crops']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'diseased_crops', 'value' => $diseasedCrops, 'description' => 'Percentage of diseased crops']);
            }

            Logger::info('Report created successfully', ['report_id' => $report->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ReportResource($report->load(['user', 'farm', 'reportDetails'])),
                'message' => 'Report created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Logger::error('Validation failed for report creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Logger::error('Failed to create report', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the report. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $report = Report::with('user', 'farm', 'reportDetails')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new ReportResource($report),
                'message' => 'Report retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Logger::error('Failed to retrieve report', ['report_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Report with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $report = Report::findOrFail($id);

            $validated = $request->validate([
                'type' => 'required|in:crop_health,resource_usage,environmental_conditions,alert_history,system_performance',
                'user_id' => 'required|exists:users,id',
                'farm_id' => 'required|exists:farms,id',
            ]);

            // تحديث التقرير الأساسي
            $report->type = $request->type;
            $report->user_id = $request->user_id;
            $report->farm_id = $request->farm_id;
            $report->save();

            // حذف التفاصيل القديمة
            $report->reportDetails()->delete();

            // تحديد البيانات بناءً على نوع التقرير (تلقائيًا) وتخزينها في report_details
            if ($request->type === 'system_performance') {
                $activeSensors = Sensor::where('status', 'active')->where('farm_id', $request->farm_id)->count();
                $faultySensors = Sensor::where('status', 'faulty')->where('farm_id', $request->farm_id)->count();
                $communicationErrors = Sensor::where('status', 'faulty')->where('farm_id', $request->farm_id)->count();

                ReportDetail::create(['report_id' => $report->id, 'category' => 'uptime', 'value' => 99.9, 'description' => 'System uptime percentage']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'active_sensors', 'value' => $activeSensors, 'description' => 'Number of active sensors']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'faulty_sensors', 'value' => $faultySensors, 'description' => 'Number of faulty sensors']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'communication_errors', 'value' => $communicationErrors, 'description' => 'Number of communication errors']);
            } elseif ($request->type === 'alert_history') {
                $logs = Log::where('farm_id', $request->farm_id)
                           ->where('status', 'failed')
                           ->orderBy('timestamp', 'desc')
                           ->take(5)
                           ->get();

                foreach ($logs as $log) {
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'alert', 'value' => 0, 'description' => "Alert: {$log->action} - {$log->message} at {$log->timestamp}"]);
                }
            } elseif ($request->type === 'environmental_conditions') {
                $weatherData = WeatherData::where('farm_id', $request->farm_id)
                                          ->orderBy('timestamp', 'desc')
                                          ->take(10)
                                          ->get();
                $averageTemperature = $weatherData->avg('temperature') ?? 0;
                $averageRainfall = $weatherData->avg('rainfall') ?? 0;
                $averageWindSpeed = $weatherData->avg('wind_speed') ?? 0;

                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_temperature', 'value' => $averageTemperature, 'description' => 'Average temperature (°C)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_rainfall', 'value' => $averageRainfall, 'description' => 'Average rainfall (mm)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_wind_speed', 'value' => $averageWindSpeed, 'description' => 'Average wind speed (km/h)']);
            } elseif ($request->type === 'resource_usage') {
                $waterUsage = 1000;
                $electricityUsage = 500;

                ReportDetail::create(['report_id' => $report->id, 'category' => 'water_usage', 'value' => $waterUsage, 'description' => 'Water usage (liters)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'electricity_usage', 'value' => $electricityUsage, 'description' => 'Electricity usage (kWh)']);
            } elseif ($request->type === 'crop_health') {
                $healthyCrops = 80;
                $diseasedCrops = 20;

                ReportDetail::create(['report_id' => $report->id, 'category' => 'healthy_crops', 'value' => $healthyCrops, 'description' => 'Percentage of healthy crops']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'diseased_crops', 'value' => $diseasedCrops, 'description' => 'Percentage of diseased crops']);
            }

            Logger::info('Report updated successfully', ['report_id' => $report->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ReportResource($report->load(['user', 'farm', 'reportDetails'])),
                'message' => 'Report updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Logger::error('Validation failed for report update', ['report_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Logger::error('Failed to update report', ['report_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Report with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $report = Report::findOrFail($id);
            $report->delete();

            Logger::info('Report deleted successfully', ['report_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Report with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Logger::error('Failed to delete report', ['report_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Report with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function exportToPDF($id)
    {
        try {
            $report = Report::with('reportDetails')->findOrFail($id);
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
            Logger::error('Failed to export report to PDF', ['report_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Report with ID {$id} not found or an unexpected error occurred while exporting.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}