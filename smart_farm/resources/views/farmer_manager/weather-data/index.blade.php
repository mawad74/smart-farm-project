@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Weather Data')
@section('dashboard-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-10 mx-auto">
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title">My Weather Data</h5>
                            <p class="text-success">Weather Records</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <form method="GET" action="{{ route('farmer_manager.weather-data.index') }}">
                                <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                            </form>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="farmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filter by Farm: {{ request('farm_id') ? $farms->find(request('farm_id'))->name : 'All' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="farmDropdown">
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.weather-data.index', ['sort' => request('sort'), 'search' => request('search')]) }}">All</a></li>
                                    @foreach ($farms as $farm)
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.weather-data.index', ['farm_id' => $farm->id, 'sort' => request('sort'), 'search' => request('search')]) }}">{{ $farm->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.weather-data.index', ['sort' => 'newest', 'search' => request('search'), 'farm_id' => request('farm_id')]) }}">Newest</a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.weather-data.index', ['sort' => 'oldest', 'search' => request('search'), 'farm_id' => request('farm_id')]) }}">Oldest</a></li>
                                </ul>
                            </div>
                            <a href="{{ route('farmer_manager.weather-data.create') }}" class="btn btn-success">Add New Weather Data</a>
                        </div>
                    </div>
                    @if ($weatherData->isEmpty())
                        <p class="text-muted">No weather data available.</p>
                    @else
                        <div class="row g-3">
                            @foreach ($weatherData as $data)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="weather-card" style="min-height: 270px; display: flex; flex-direction: column; justify-content: space-between;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="desc" style="font-size:1.1rem;font-weight:600;">{{ $data->farm ? $data->farm->name : 'N/A' }}</div>
                                                <div class="main" style="font-size:2rem;">{{ number_format($data->temperature, 1) }}&deg;C</div>
                                            </div>
                                            @php
                                                $temp = $data->temperature;
                                                if ($temp <= 10) {
                                                    $weatherIcon = 'https://cdn-icons-png.flaticon.com/512/414/414927.png'; // بارد جداً - ثلج
                                                } elseif ($temp > 10 && $temp <= 20) {
                                                    $weatherIcon = 'https://cdn-icons-png.flaticon.com/512/1163/1163661.png'; // غائم/معتدل
                                                } elseif ($temp > 20 && $temp <= 30) {
                                                    $weatherIcon = 'https://cdn-icons-png.flaticon.com/512/869/869869.png'; // مشمس
                                                } else {
                                                    $weatherIcon = 'https://cdn-icons-png.flaticon.com/512/169/169367.png'; // حار جداً
                                                }
                                            @endphp
                                            <img src="{{ $weatherIcon }}" class="weather-icon" style="width:48px;height:48px;" alt="weather">
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge bg-light text-dark me-1">Rainfall: {{ number_format($data->rainfall, 1) }} mm</span>
                                            <span class="badge bg-light text-dark me-1">Wind: {{ number_format($data->wind_speed, 1) }} km/h</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge bg-light text-dark me-1">Date: {{ $data->date ? $data->date->format('d M, Y') : 'N/A' }}</span>
                                            <span class="badge bg-light text-dark me-1">Created: {{ $data->created_at ? $data->created_at->format('d M, Y') : 'N/A' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-end align-items-center mt-auto" style="gap:6px;">
                                            <a href="{{ route('farmer_manager.weather-data.edit', $data->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="Edit" style="height:32px;width:32px;padding:0;">
                                                <i class="fa-solid fa-pen-to-square" style="font-size:1rem;"></i>
                                            </a>
                                            <form action="{{ route('farmer_manager.weather-data.destroy', $data->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" title="Delete" style="height:32px;width:32px;padding:0;">
                                                    <i class="fa-solid fa-trash-can" style="font-size:1rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <p class="text-muted">Showing {{ $weatherData->firstItem() }} to {{ $weatherData->lastItem() }} of {{ $weatherData->total() }} data</p>
                            {{ $weatherData->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .d-flex.gap-2 {
            gap: 10px !important;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .text-success {
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }

        .btn-outline-secondary {
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .btn-success {
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-outline-primary, .btn-outline-danger {
            padding: 5px 10px;
        }

        .pagination .page-link {
            color: #4a7c59;
        }

        .pagination .page-item.active .page-link {
            background-color: #4a7c59;
            border-color: #4a7c59;
            color: #fff;
        }

        .weather-card {
            background: #f3f6d9 !important;
            border-radius: 18px;
            box-shadow: 0 6px 18px 0 rgba(0,0,0,0.13);
            padding: 16px 12px 10px 12px;
            margin-bottom: 14px;
            transition: box-shadow 0.2s;
            border: 1.5px solid #e6eec7;
            min-height: 180px;
        }
        .weather-card .main {
            font-size: 1.2rem;
        }
        .weather-card .desc {
            font-size: 0.98rem;
        }
        .weather-card .btn-outline-primary, .weather-card .btn-outline-danger {
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .weather-card .btn-outline-primary:hover {
            background: #e6eec7;
            color: #3d5c0a;
        }
        .weather-card .btn-outline-danger:hover {
            background: #ffeaea;
            color: #b30000;
        }
        @media (max-width: 767px) {
            .weather-card {
                padding: 10px 6px 8px 6px;
            }
            .weather-card .main {
                font-size: 1rem;
            }
            .weather-card .desc {
                font-size: 0.85rem;
            }
        }
    </style>
@endsection