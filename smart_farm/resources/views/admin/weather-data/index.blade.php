@extends('layouts.dashboard')

@section('title', 'All Weather Data')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Weather Data</h5>
                                <p class="text-success">Weather Records</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.weather-data.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="farmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Farm: {{ request('farm_id') ? $farms->find(request('farm_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="farmDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.weather-data.index', ['sort' => request('sort'), 'search' => request('search')]) }}">All</a></li>
                                        @foreach ($farms as $farm)
                                            <li><a class="dropdown-item" href="{{ route('admin.weather-data.index', ['farm_id' => $farm->id, 'sort' => request('sort'), 'search' => request('search')]) }}">{{ $farm->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.weather-data.index', ['sort' => 'newest', 'search' => request('search'), 'farm_id' => request('farm_id')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.weather-data.index', ['sort' => 'oldest', 'search' => request('search'), 'farm_id' => request('farm_id')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.weather-data.create') }}" class="btn btn-success">Add New Weather Data</a>
                            </div>
                        </div>
                        @if ($weatherData->isEmpty())
                            <p class="text-muted">No weather data available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Farm</th>
                                        <th>Temperature (°C)</th>
                                        <th>Rainfall (mm)</th>
                                        <th>Wind Speed (km/h)</th>
                                        <th>Timestamp</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($weatherData as $data)
                                        <tr>
                                            <td>{{ $data->id }}</td>
                                            <td>{{ $data->farm ? $data->farm->name : 'N/A' }}</td>
                                            <td>{{ number_format($data->temperature, 2) }}</td>
                                            <td>{{ number_format($data->rainfall, 2) }}</td>
                                            <td>{{ number_format($data->wind_speed, 2) }}</td>
                                            <td>{{ $data->timestamp->format('d M, Y H:i:s') }}</td>
                                            <td>{{ $data->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.weather-data.edit', $data->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.weather-data.destroy', $data->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this weather data?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
    <style>
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
    </style>
@endsection