<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportDetail;
use App\User;
use App\Models\Sensor;
use App\Models\Farm;
use App\Models\WeatherData;
use App\Models\FinancialRecord;
use App\Models\Log;
use App\Models\ReportRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log as Logger;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Notification;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $type = $request->input('type');

        $reportsQuery = Report::with('user');

        // البحث
        if ($search) {
            $reportsQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }

        // فلترة حسب النوع
        if ($type) {
            $reportsQuery->where('type', $type);
        }

        // الترتيب
        if ($sort === 'newest') {
            $reportsQuery->latest();
        } elseif ($sort === 'oldest') {
            $reportsQuery->oldest();
        }

        $reports = $reportsQuery->paginate(10);
        $pendingRequests = ReportRequest::where('status', 'pending')->with('user', 'farm')->get();

        return view('admin.reports.index', compact('reports', 'pendingRequests'));
    }

    public function create()
    {
        $users = User::all();
        $farms = Farm::all();
        return view('admin.reports.create', compact('users', 'farms'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:crop_health,resource_usage,environmental_conditions,alert_history,system_performance',
                'user_id' => 'required|exists:users,id',
                'farm_id' => 'required|exists:farms,id',
            ]);

            // إنشاء التقرير الأساسي
            $report = new Report();
            $report->type = $request->type;
            $report->user_id = $request->user_id;
            $report->farm_id = $request->farm_id;
            $report->save();

            // تحديد البيانات بناءً على نوع التقرير (تلقائيًا) وتخزينها في report_details
            if ($request->type === 'system_performance') {
                // سحب بيانات الأداء من جدول sensors
                $activeSensors = Sensor::where('status', 'active')->where('farm_id', $request->farm_id)->count();
                $faultySensors = Sensor::where('status', 'faulty')->where('farm_id', $request->farm_id)->count();
                $communicationErrors = Sensor::where('status', 'faulty')->where('farm_id', $request->farm_id)->count();

                // تخزين البيانات في report_details
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'uptime',
                    'value' => 99.9, // مثال، ممكن تحتاجي مصدر للـ uptime
                    'description' => 'System uptime percentage',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'active_sensors',
                    'value' => $activeSensors,
                    'description' => 'Number of active sensors',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'faulty_sensors',
                    'value' => $faultySensors,
                    'description' => 'Number of faulty sensors',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'communication_errors',
                    'value' => $communicationErrors,
                    'description' => 'Number of communication errors',
                ]);
            } elseif ($request->type === 'alert_history') {
                // سحب بيانات التنبيهات (افتراضيًا من جدول logs)
                $logs = Log::where('farm_id', $request->farm_id)
                           ->where('status', 'failed')
                           ->orderBy('timestamp', 'desc')
                           ->take(5)
                           ->get();

                foreach ($logs as $log) {
                    ReportDetail::create([
                        'report_id' => $report->id,
                        'category' => 'alert',
                        'value' => 0, // لا قيمة عددية هنا
                        'description' => "Alert: {$log->action} - {$log->message} at {$log->timestamp}",
                    ]);
                }
            } elseif ($request->type === 'environmental_conditions') {
                // سحب بيانات الطقس
                $weatherData = WeatherData::where('farm_id', $request->farm_id)
                                          ->orderBy('timestamp', 'desc')
                                          ->take(10)
                                          ->get();
                $averageTemperature = $weatherData->avg('temperature') ?? 0;
                $averageRainfall = $weatherData->avg('rainfall') ?? 0;
                $averageWindSpeed = $weatherData->avg('wind_speed') ?? 0;

                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'average_temperature',
                    'value' => $averageTemperature,
                    'description' => 'Average temperature (°C)',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'average_rainfall',
                    'value' => $averageRainfall,
                    'description' => 'Average rainfall (mm)',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'average_wind_speed',
                    'value' => $averageWindSpeed,
                    'description' => 'Average wind speed (km/h)',
                ]);
            } elseif ($request->type === 'resource_usage') {
                // سحب بيانات الاستخدام (افتراضيًا، ممكن تحتاجي جدول منفصل للـ resources)
                $waterUsage = 1000; // مثال، ممكن تحتاجي مصدر للـ water usage
                $electricityUsage = 500; // مثال

                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'water_usage',
                    'value' => $waterUsage,
                    'description' => 'Water usage (liters)',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'electricity_usage',
                    'value' => $electricityUsage,
                    'description' => 'Electricity usage (kWh)',
                ]);
            } elseif ($request->type === 'crop_health') {
                // سحب بيانات صحة المحاصيل (افتراضيًا، ممكن تحتاجي جدول منفصل)
                $healthyCrops = 80; // مثال
                $diseasedCrops = 20; // مثال

                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'healthy_crops',
                    'value' => $healthyCrops,
                    'description' => 'Percentage of healthy crops',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'diseased_crops',
                    'value' => $diseasedCrops,
                    'description' => 'Percentage of diseased crops',
                ]);
            }

            Logger::info('Report created successfully', ['report_id' => $report->id]);
            return redirect()->route('admin.reports.index')->with('success', 'Report created successfully.');
        } catch (ValidationException $e) {
            Logger::error('Validation failed for report creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Logger::error('Failed to create report', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create report: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $report = Report::with('user', 'farm', 'reportDetails')->findOrFail($id);
        return view('admin.reports.show', compact('report'));
    }

    public function edit($id)
    {
        $report = Report::with('reportDetails')->findOrFail($id);
        $users = User::all();
        $farms = Farm::all();
        return view('admin.reports.edit', compact('report', 'users', 'farms'));
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        try {
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

                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'uptime',
                    'value' => 99.9,
                    'description' => 'System uptime percentage',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'active_sensors',
                    'value' => $activeSensors,
                    'description' => 'Number of active sensors',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'faulty_sensors',
                    'value' => $faultySensors,
                    'description' => 'Number of faulty sensors',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'communication_errors',
                    'value' => $communicationErrors,
                    'description' => 'Number of communication errors',
                ]);
            } elseif ($request->type === 'alert_history') {
                $logs = Log::where('farm_id', $request->farm_id)
                           ->where('status', 'failed')
                           ->orderBy('timestamp', 'desc')
                           ->take(5)
                           ->get();

                foreach ($logs as $log) {
                    ReportDetail::create([
                        'report_id' => $report->id,
                        'category' => 'alert',
                        'value' => 0,
                        'description' => "Alert: {$log->action} - {$log->message} at {$log->timestamp}",
                    ]);
                }
            } elseif ($request->type === 'environmental_conditions') {
                $weatherData = WeatherData::where('farm_id', $request->farm_id)
                                          ->orderBy('timestamp', 'desc')
                                          ->take(10)
                                          ->get();
                $averageTemperature = $weatherData->avg('temperature') ?? 0;
                $averageRainfall = $weatherData->avg('rainfall') ?? 0;
                $averageWindSpeed = $weatherData->avg('wind_speed') ?? 0;

                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'average_temperature',
                    'value' => $averageTemperature,
                    'description' => 'Average temperature (°C)',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'average_rainfall',
                    'value' => $averageRainfall,
                    'description' => 'Average rainfall (mm)',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'average_wind_speed',
                    'value' => $averageWindSpeed,
                    'description' => 'Average wind speed (km/h)',
                ]);
            } elseif ($request->type === 'resource_usage') {
                $waterUsage = 1000;
                $electricityUsage = 500;

                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'water_usage',
                    'value' => $waterUsage,
                    'description' => 'Water usage (liters)',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'electricity_usage',
                    'value' => $electricityUsage,
                    'description' => 'Electricity usage (kWh)',
                ]);
            } elseif ($request->type === 'crop_health') {
                $healthyCrops = 80;
                $diseasedCrops = 20;

                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'healthy_crops',
                    'value' => $healthyCrops,
                    'description' => 'Percentage of healthy crops',
                ]);
                ReportDetail::create([
                    'report_id' => $report->id,
                    'category' => 'diseased_crops',
                    'value' => $diseasedCrops,
                    'description' => 'Percentage of diseased crops',
                ]);
            }

            Logger::info('Report updated successfully', ['report_id' => $report->id]);
            return redirect()->route('admin.reports.index')->with('success', 'Report updated successfully.');
        } catch (ValidationException $e) {
            Logger::error('Validation failed for report update', ['report_id' => $report->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Logger::error('Failed to update report', ['report_id' => $report->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update report: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $report = Report::findOrFail($id);
            $report->delete();

            Logger::info('Report deleted successfully', ['report_id' => $id]);
            return redirect()->route('admin.reports.index')->with('success', 'Report deleted successfully.');
        } catch (\Exception $e) {
            Logger::error('Failed to delete report', ['report_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.reports.index')->with('error', 'Failed to delete report: ' . $e->getMessage());
        }
    }

    public function exportToPDF($id)
    {
        $report = Report::with('reportDetails')->findOrFail($id);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('report'));
        return $pdf->download('report-' . $report->id . '.pdf');
    }

    public function approveRequest($id)
    {
        $request = ReportRequest::findOrFail($id);
        if ($request->status === 'pending') {
            $report = new Report();
            $report->user_id = $request->user_id;
            $report->farm_id = $request->farm_id;
            $report->type = $request->type;
            $report->save();

            // تحديد البيانات بناءً على نوع التقرير
            if ($request->type === 'system_performance') {
                $activeSensors = Sensor::where('status', 'active')->where('farm_id', $request->farm_id)->count();
                $faultySensors = Sensor::where('status', 'faulty')->where('farm_id', $request->farm_id)->count();
                $communicationErrors = Sensor::where('status', 'faulty')->where('farm_id', $request->farm_id)->count();

                ReportDetail::create(['report_id' => $report->id, 'category' => 'uptime', 'value' => 99.9, 'description' => 'System uptime percentage']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'active_sensors', 'value' => $activeSensors, 'description' => 'Number of active sensors']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'faulty_sensors', 'value' => $faultySensors, 'description' => 'Number of faulty sensors']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'communication_errors', 'value' => $communicationErrors, 'description' => 'Number of communication errors']);
            } elseif ($request->type === 'alert_history') {
                $logs = Log::where('farm_id', $request->farm_id)->where('status', 'failed')->orderBy('timestamp', 'desc')->take(5)->get();
                foreach ($logs as $log) {
                    ReportDetail::create(['report_id' => $report->id, 'category' => 'alert', 'value' => 0, 'description' => "Alert: {$log->action} - {$log->message} at {$log->timestamp}"]);
                }
            } elseif ($request->type === 'environmental_conditions') {
                $weatherData = WeatherData::where('farm_id', $request->farm_id)->orderBy('timestamp', 'desc')->take(10)->get();
                $averageTemperature = $weatherData->avg('temperature') ?? 0;
                $averageRainfall = $weatherData->avg('rainfall') ?? 0;
                $averageWindSpeed = $weatherData->avg('wind_speed') ?? 0;

                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_temperature', 'value' => $averageTemperature, 'description' => 'Average temperature (°C)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_rainfall', 'value' => $averageRainfall, 'description' => 'Average rainfall (mm)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'average_wind_speed', 'value' => $averageWindSpeed, 'description' => 'Average wind speed (km/h)']);
            } elseif ($request->type === 'resource_usage') {
                ReportDetail::create(['report_id' => $report->id, 'category' => 'water_usage', 'value' => 1000, 'description' => 'Water usage (liters)']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'electricity_usage', 'value' => 500, 'description' => 'Electricity usage (kWh)']);
            } elseif ($request->type === 'crop_health') {
                ReportDetail::create(['report_id' => $report->id, 'category' => 'healthy_crops', 'value' => 80, 'description' => 'Percentage of healthy crops']);
                ReportDetail::create(['report_id' => $report->id, 'category' => 'diseased_crops', 'value' => 20, 'description' => 'Percentage of diseased crops']);
            }

            // إضافة إشعار للفارمر
            \App\Models\Notification::create([
                'user_id' => $request->user_id,
                'type' => 'report_request_approved',
                'message' => "Your report request for " . ucfirst(str_replace('_', ' ', $request->type)) . " on " . $request->farm->name . " has been approved.",
                'is_read' => false,
            ]);

            $request->update(['status' => 'approved']);
            Logger::info('Report request approved', ['request_id' => $request->id, 'report_id' => $report->id]);

            return redirect()->route('admin.reports.index')->with('success', 'Report request approved and created successfully.');
        }

        return redirect()->route('admin.reports.index')->with('error', 'Invalid request status.');
    }

    public function rejectRequest($id)
    {
        $request = ReportRequest::findOrFail($id);
        if ($request->status === 'pending') {
            $request->update(['status' => 'rejected']);
            Logger::info('Report request rejected', ['request_id' => $request->id]);

            return redirect()->route('admin.reports.index')->with('success', 'Report request rejected.');
        }

        return redirect()->route('admin.reports.index')->with('error', 'Invalid request status.');
    }
}