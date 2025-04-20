@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('dashboard-content')
    <div class="container-fluid dashboard-container" style="background: url('https://images.unsplash.com/photo-1500595046743-dd26eb716e7e') no-repeat center center/cover; background-attachment: fixed;">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark">Smart Farm Dashboard</h2>
            <div>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="d-inline-block">
                    <select name="farm_id" class="form-select d-inline-block" onchange="this.form.submit()">
                        <option value="">Select Farm</option>
                        @foreach ($farms as $farm)
                            <option value="{{ $farm->id }}" {{ $farm->id == request('farm_id') ? 'selected' : '' }}>{{ $farm->name }}</option>
                        @endforeach
                    </select>
                </form>
                <button class="btn btn-outline-secondary ms-2" onclick="location.reload()">Refresh</button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3 text-center">
                <div class="farm-card">
                    <i class="fas fa-tractor fa-2x mb-2 text-success"></i>
                    <h6 class="card-title">Total Farms</h6>
                    <h3 class="count-up text-dark" data-target="{{ $stats['farms_count'] ?? 0 }}">0</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3 text-center">
                <div class="farm-card">
                    <i class="fas fa-microchip fa-2x mb-2 text-success"></i>
                    <h6 class="card-title">Active Sensors</h6>
                    <h3 class="count-up text-dark" data-target="{{ $stats['active_sensors_count'] ?? 0 }}">0</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3 text-center">
                <div class="farm-card">
                    <i class="fas fa-bell fa-2x mb-2 text-success"></i>
                    <h6 class="card-title">New Alerts</h6>
                    <h3 class="count-up text-dark" data-target="{{ $stats['new_alerts_count'] ?? 0 }}">0</h3>
                </div>
            </div>
            <div class="col-md-3 mb-3 text-center">
                <div class="farm-card">
                    <i class="fas fa-cogs fa-2x mb-2 text-success"></i>
                    <h6 class="card-title">Executed Commands</h6>
                    <h3 class="count-up text-dark" data-target="{{ $stats['executed_commands_count'] ?? 0 }}">0</h3>
                </div>
            </div>
        </div>

        <!-- Visualizations -->
        <div class="row mb-4">
            <!-- Crops Distribution (Donut Chart) -->
            <div class="col-md-6 mb-4">
                <div class="farm-card">
                    <h5 class="card-title">Crops Distribution</h5>
                    <div id="cropsChart" style="height: 300px;"></div>
                </div>
            </div>

            <!-- Sensor Readings (Line Chart) -->
            <div class="col-md-6 mb-4">
                <div class="farm-card">
                    <h5 class="card-title">Temperature Trends</h5>
                    <div id="temperatureChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Current Readings (Progress Circles) -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4 text-center">
                <div class="farm-card">
                    <h5 class="card-title">Current Temperature</h5>
                    <div id="temperatureCircle" style="height: 200px; width: 200px; margin: 0 auto;"></div>
                    <p class="mt-2">{{ $current_readings['temperature'] ?? 'N/A' }} °C</p>
                </div>
            </div>
            <div class="col-md-6 mb-4 text-center">
                <div class="farm-card">
                    <h5 class="card-title">Current Humidity</h5>
                    <div id="humidityCircle" style="height: 200px; width: 200px; margin: 0 auto;"></div>
                    <p class="mt-2">{{ $current_readings['humidity'] ?? 'N/A' }} %</p>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="farm-card">
                    <h5 class="card-title">Recent Alerts</h5>
                    @if (!empty($alerts))
                        <div class="alert-list">
                            @foreach ($alerts as $alert)
                                <div class="alert alert-{{ $alert->severity == 'high' ? 'danger' : ($alert->severity == 'medium' ? 'warning' : 'info') }} alert-dismissible fade show" role="alert">
                                    <strong>{{ $alert->message }}</strong> - {{ $alert->created_at->format('M d, Y H:i') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No recent alerts.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .dashboard-container {
            min-height: calc(100vh - 70px);
            padding: 20px;
            background: linear-gradient(rgba(255, 245, 230, 0.8), rgba(255, 245, 230, 0.8)), url('https://images.unsplash.com/photo-1500595046743-dd26eb716e7e') no-repeat center center/cover;
            background-attachment: fixed;
        }

        .farm-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .farm-card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .count-up {
            font-size: 2rem;
            font-weight: bold;
        }

        .alert-list .alert {
            margin-bottom: 10px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
        }

        @media (max-width: 768px) {
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
@endsection

@section('scripts')
    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <!-- Animated Numbers -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Animated Numbers
            const counters = document.querySelectorAll('.count-up');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                let count = 0;
                const speed = 200; // Speed of counting
                const increment = target / speed;

                const updateCount = () => {
                    count += increment;
                    if (count < target) {
                        counter.textContent = Math.ceil(count);
                        setTimeout(updateCount, 10);
                    } else {
                        counter.textContent = target;
                    }
                };
                updateCount();
            });

            // Crops Distribution (Donut Chart)
            let cropsData = @json($plants ?? []);
            let cropsSeries = cropsData && Object.keys(cropsData).length > 0 ? Object.keys(cropsData).map((key, index) => ({
                name: key,
                y: Number(cropsData[key]) || 0,
                color: index === 0 ? '#4a7c59' : index === 1 ? '#8bc34a' : index === 2 ? '#c5e1a5' : index === 3 ? '#a5d6a7' : '#e0e0e0'
            })) : [{ name: 'No Data', y: 1, color: '#e0e0e0' }];

            try {
                Highcharts.chart('cropsChart', {
                    chart: {
                        type: 'pie',
                        height: 300,
                        backgroundColor: 'rgba(0,0,0,0)'
                    },
                    title: {
                        text: null
                    },
                    plotOptions: {
                        pie: {
                            innerSize: '50%',
                            depth: 45,
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}: {point.percentage:.1f} %',
                                style: { color: '#2c3e50' }
                            }
                        }
                    },
                    series: [{
                        name: 'Crops',
                        data: cropsSeries
                    }]
                });
            } catch (e) {
                console.error('Error initializing Crops Chart:', e);
            }

            // Temperature Trends (Line Chart)
            let temperatureData = @json($temperature_data ?? ['labels' => ['No Data'], 'data' => [0]]);
            let temperatureLabels = temperatureData && temperatureData.labels && temperatureData.labels.length > 0 ? temperatureData.labels : ['No Data'];
            let temperatureValues = temperatureData && temperatureData.data && temperatureData.data.length > 0 ? temperatureData.data.map(val => Number(val) || 0) : [0];

            try {
                Highcharts.chart('temperatureChart', {
                    chart: {
                        type: 'line',
                        height: 300,
                        backgroundColor: 'rgba(0,0,0,0)'
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: temperatureLabels,
                        labels: { style: { color: '#2c3e50' } }
                    },
                    yAxis: {
                        title: {
                            text: 'Temperature (°C)',
                            style: { color: '#2c3e50' }
                        },
                        labels: { style: { color: '#2c3e50' } }
                    },
                    series: [{
                        name: 'Temperature',
                        data: temperatureValues,
                        color: '#4a7c59',
                        marker: {
                            symbol: 'circle',
                            radius: 5,
                            fillColor: '#8bc34a'
                        }
                    }],
                    tooltip: {
                        valueSuffix: ' °C'
                    }
                });
            } catch (e) {
                console.error('Error initializing Temperature Chart:', e);
            }

            // Temperature Progress Circle
            try {
                Highcharts.chart('temperatureCircle', {
                    chart: {
                        type: 'solidgauge',
                        height: 200,
                        backgroundColor: 'rgba(0,0,0,0)'
                    },
                    title: {
                        text: null
                    },
                    pane: {
                        center: ['50%', '50%'],
                        size: '100%',
                        startAngle: -90,
                        endAngle: 90,
                        background: {
                            backgroundColor: '#e0e0e0',
                            innerRadius: '60%',
                            outerRadius: '100%',
                            shape: 'arc'
                        }
                    },
                    yAxis: {
                        min: 0,
                        max: 50,
                        stops: [
                            [0.1, '#4a7c59'],
                            [0.5, '#8bc34a'],
                            [0.9, '#c5e1a5']
                        ],
                        lineWidth: 0,
                        tickWidth: 0,
                        minorTickInterval: null,
                        tickAmount: 2,
                        labels: {
                            y: 16,
                            style: { color: '#2c3e50' }
                        }
                    },
                    series: [{
                        name: 'Temperature',
                        data: [{{ $current_readings['temperature'] ?? 0 }}],
                        dataLabels: {
                            format: '{y} °C',
                            borderWidth: 0,
                            useHTML: true,
                            style: { color: '#2c3e50' }
                        },
                        tooltip: {
                            valueSuffix: ' °C'
                        }
                    }],
                    credits: {
                        enabled: false
                    }
                });
            } catch (e) {
                console.error('Error initializing Temperature Circle:', e);
            }

            // Humidity Progress Circle
            try {
                Highcharts.chart('humidityCircle', {
                    chart: {
                        type: 'solidgauge',
                        height: 200,
                        backgroundColor: 'rgba(0,0,0,0)'
                    },
                    title: {
                        text: null
                    },
                    pane: {
                        center: ['50%', '50%'],
                        size: '100%',
                        startAngle: -90,
                        endAngle: 90,
                        background: {
                            backgroundColor: '#e0e0e0',
                            innerRadius: '60%',
                            outerRadius: '100%',
                            shape: 'arc'
                        }
                    },
                    yAxis: {
                        min: 0,
                        max: 100,
                        stops: [
                            [0.1, '#4a7c59'],
                            [0.5, '#8bc34a'],
                            [0.9, '#c5e1a5']
                        ],
                        lineWidth: 0,
                        tickWidth: 0,
                        minorTickInterval: null,
                        tickAmount: 2,
                        labels: {
                            y: 16,
                            style: { color: '#2c3e50' }
                        }
                    },
                    series: [{
                        name: 'Humidity',
                        data: [{{ $current_readings['humidity'] ?? 0 }}],
                        dataLabels: {
                            format: '{y} %',
                            borderWidth: 0,
                            useHTML: true,
                            style: { color: '#2c3e50' }
                        },
                        tooltip: {
                            valueSuffix: ' %'
                        }
                    }],
                    credits: {
                        enabled: false
                    }
                });
            } catch (e) {
                console.error('Error initializing Humidity Circle:', e);
            }
        });
    </script>
@endsection