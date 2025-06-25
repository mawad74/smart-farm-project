<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportRequest;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\Sensor;
use App\Models\WeatherData;
use App\Models\Log;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;

class AdminReportRequestController extends Controller
{
    public function index()
    {
        $requests = ReportRequest::where('status', 'pending')->with('user', 'farm')->get();
        return view('admin.report_requests.index', compact('requests'));
    }

    public function update(Request $request, $id)
    {
        $requestData = ReportRequest::findOrFail($id);

        try {
            $action = $request->input('action');
            if ($action === 'approve') {
                $report = new Report();
                $report->user_id = $requestData->user_id;
                $report->farm_id = $requestData->farm_id;
                $report->type = $requestData->type;
                $report->save();

                // إضافة التفاصيل بناءً على النوع
                if ($requestData->type === 'system_performance') {
                    $activeSensors = Sensor::where('status', 'active')->where('farm_id', $requestData->farm_id)->count();
                    $faultySensors = Sensor::where('status', 'faulty')->where('farm_id', $requestData->farm_id)->count();
                    $communicationErrors = Sensor::where('status', 'faulty')->where('farm_id', $requestData->farm_id)->count();

                    ReportDetail::create(['report_id' => $report->id, 'category' => 'uptime', 'value' => 99.9, 'description' => 'System uptime percentage']);
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'active_sensors', 'value' => $activeSensors, 'description' => 'Number of active sensors']);
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'faulty_sensors', 'value' => $faultySensors, 'description' => 'Number of faulty sensors']);
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'communication_errors', 'value' => $communicationErrors, 'description' => 'Number of communication errors']);
                } elseif ($requestData->type === 'alert_history') {
                    $logs = Log::where('farm_id', $requestData->farm_id)->where('status', 'failed')->orderBy('timestamp', 'desc')->take(5)->get();
                    foreach ($logs as $log) {
                        ReportDetail::create(['report_id' => $report->id, 'category' => 'alert', 'value' => 0, 'description' => "Alert: {$log->action} - {$log->message} at {$log->timestamp}"]);
                    }
                } elseif ($requestData->type === 'environmental_conditions') {
                    $weatherData = WeatherData::where('farm_id', $requestData->farm_id)->orderBy('timestamp', 'desc')->take(10)->get();
                    $averageTemperature = $weatherData->avg('temperature') ?? 0;
                    $averageRainfall = $weatherData->avg('rainfall') ?? 0;
                    $averageWindSpeed = $weatherData->avg('wind_speed') ?? 0;

                    ReportDetail::create(['report_id' => $report->id, 'category' => 'average_temperature', 'value' => $averageTemperature, 'description' => 'Average temperature (°C)']);
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'average_rainfall', 'value' => $averageRainfall, 'description' => 'Average rainfall (mm)']);
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'average_wind_speed', 'value' => $averageWindSpeed, 'description' => 'Average wind speed (km/h)']);
                } elseif ($requestData->type === 'resource_usage') {
                    $waterUsage = 1000;
                    $electricityUsage = 500;

                    ReportDetail::create(['report_id' => $report->id, 'category' => 'water_usage', 'value' => $waterUsage, 'description' => 'Water usage (liters)']);
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'electricity_usage', 'value' => $electricityUsage, 'description' => 'Electricity usage (kWh)']);
                } elseif ($requestData->type === 'crop_health') {
                    $healthyCrops = 80;
                    $diseasedCrops = 20;

                    ReportDetail::create(['report_id' => $report->id, 'category' => 'healthy_crops', 'value' => $healthyCrops, 'description' => 'Percentage of healthy crops']);
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'diseased_crops', 'value' => $diseasedCrops, 'description' => 'Percentage of diseased crops']);
                }

                $requestData->update(['status' => 'approved']);
                Log::info('Report request approved', ['request_id' => $id, 'report_id' => $report->id]);
            } elseif ($action === 'reject') {
                $requestData->update(['status' => 'rejected']);
                Log::info('Report request rejected', ['request_id' => $id]);
            }

            return redirect()->route('admin.report_requests.index')->with('success', 'Report request ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update report request', ['request_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update report request: ' . $e->getMessage());
        }
    }
}