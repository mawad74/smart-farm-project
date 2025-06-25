<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\Sensor;
use App\Models\Alert;
use App\Models\ControlCommand;
use App\Models\Plant;
use App\Models\ReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $farm_id = $request->input('farm_id');
        $stats = [
            'farms_count' => Farm::count(),
            'active_sensors_count' => Sensor::where('status', 'active')->count(),
            'new_alerts_count' => Alert::where('status', 'new')->count(),
            'executed_commands_count' => ControlCommand::where('status', true)->count(),
        ];

        $pendingRequestsCount = ReportRequest::where('status', 'pending')->count();
        $farms = Farm::all();
        $plants_query = Plant::query();
        if ($farm_id) {
            $plants_query->where('farm_id', $farm_id);
        }
        $plants = $plants_query->select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($plant) {
                return [$plant->type => $plant->count];
            })->toArray();

        $farm_info = null;
        if ($farm_id) {
            $farm = Farm::find($farm_id);
            if ($farm) {
                $farm_info = [
                    'name' => $farm->name,
                    'crop_health' => $this->calculateCropHealth($farm_id),
                    'sowing_date' => $this->getSowingDate($farm_id),
                    'harvest_date' => $this->getHarvestDate($farm_id),
                ];
            }
        }

        $crops_percentage = $plants_query->select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->map(function ($plant) use ($plants_query) {
                $total = $plants_query->count();
                return [
                    'type' => $plant->type,
                    'percentage' => $total > 0 ? round(($plant->count / $total) * 100, 2) : 0,
                ];
            })->toArray();

        $current_readings = $this->getCurrentReadings($farm_id);
        $temperature_data = $this->getTemperatureData($farm_id, $request->input('period', 'monthly'));

        return view('admin.dashboard', compact('stats', 'farms', 'plants', 'farm_info', 'crops_percentage', 'current_readings', 'temperature_data', 'pendingRequestsCount'));
    }

    private function calculateCropHealth($farm_id)
    {
        $alerts_count = Alert::whereHas('sensor', function ($query) use ($farm_id) {
            $query->where('farm_id', $farm_id);
        })->where('status', 'new')->count();

        return $alerts_count == 0 ? 'Good' : ($alerts_count < 3 ? 'Moderate' : 'Poor');
    }

    private function getSowingDate($farm_id)
    {
        $plant = Plant::where('farm_id', $farm_id)->first();
        return $plant ? $plant->created_at->format('M d, Y') : 'N/A';
    }

    private function getHarvestDate($farm_id)
    {
        $plant = Plant::where('farm_id', $farm_id)->first();
        return $plant ? $plant->created_at->addDays(60)->format('M d, Y') : 'N/A';
    }

    private function getTemperatureData($farm_id, $period)
    {
        $labels = [];
        $data = [];

        try {
            if (!Schema::hasTable('sensor_data')) {
                \Illuminate\Support\Facades\Log::warning('SensorData table does not exist.');
                return [
                    'labels' => ['No Data'],
                    'data' => [0],
                ];
            }

            $query = \App\Models\SensorData::query()
                ->join('sensors', 'sensor_data.sensor_id', '=', 'sensors.id')
                ->where('sensors.type', 'temperature');

            if ($farm_id) {
                $query->where('sensors.farm_id', $farm_id);
            }

            if ($period == 'yearly') {
                $query->selectRaw('YEAR(sensor_data.timestamp) as period, AVG(sensor_data.value) as avg_value')
                    ->groupBy('period')
                    ->orderBy('period');
                $results = $query->get();

                $labels = $results->pluck('period')->toArray();
                $data = $results->pluck('avg_value')->map(function ($value) {
                    return round($value, 2);
                })->toArray();
            } elseif ($period == 'monthly') {
                $query->selectRaw('DATE_FORMAT(sensor_data.timestamp, "%b %Y") as period, AVG(sensor_data.value) as avg_value')
                    ->groupBy('period')
                    ->orderBy('sensor_data.timestamp');
                $results = $query->get();

                $labels = $results->pluck('period')->toArray();
                $data = $results->pluck('avg_value')->map(function ($value) {
                    return round($value, 2);
                })->toArray();
            } elseif ($period == 'weekly') {
                $query->selectRaw('WEEK(sensor_data.timestamp, 1) as period, AVG(sensor_data.value) as avg_value')
                    ->groupBy('period')
                    ->orderBy('sensor_data.timestamp');
                $results = $query->get();

                $labels = $results->pluck('period')->map(function ($week) {
                    return "Week $week";
                })->toArray();
                $data = $results->pluck('avg_value')->map(function ($value) {
                    return round($value, 2);
                })->toArray();
            }

            if (empty($labels)) {
                $labels = ['No Data'];
                $data = [0];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching temperature data: ' . $e->getMessage());
            $labels = ['No Data'];
            $data = [0];
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getCurrentReadings($farm_id)
    {
        $readings = [
            'temperature' => null,
            'humidity' => null,
        ];

        try {
            if (!Schema::hasTable('sensor_data')) {
                \Illuminate\Support\Facades\Log::warning('SensorData table does not exist.');
                return $readings;
            }
            $temperature = \App\Models\SensorData::query()
                ->join('sensors', 'sensor_data.sensor_id', '=', 'sensors.id')
                ->where('sensors.type', 'temperature')
                ->when($farm_id, function ($query) use ($farm_id) {
                    $query->where('sensors.farm_id', $farm_id);
                })
                ->orderBy('sensor_data.timestamp', 'desc')
                ->first();
            $humidity = \App\Models\SensorData::query()
                ->join('sensors', 'sensor_data.sensor_id', '=', 'sensors.id')
                ->where('sensors.type', 'humidity')
                ->when($farm_id, function ($query) use ($farm_id) {
                    $query->where('sensors.farm_id', $farm_id);
                })
                ->orderBy('sensor_data.timestamp', 'desc')
                ->first();

            $readings['temperature'] = $temperature ? round($temperature->value, 2) : null;
            $readings['humidity'] = $humidity ? round($humidity->value, 2) : null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching current readings: ' . $e->getMessage());
        }

        return $readings;
    }
}