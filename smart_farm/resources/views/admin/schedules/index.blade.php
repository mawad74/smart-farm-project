@extends('layouts.dashboard')

@section('title', 'All Schedules')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Schedules</h5>
                                <p class="text-success">Active Schedules</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.schedules.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="farmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Farm: {{ request('farm_id') ? $farms->find(request('farm_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="farmDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['sort' => request('sort'), 'search' => request('search'), 'plant_id' => request('plant_id'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($farms as $farm)
                                            <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['farm_id' => $farm->id, 'sort' => request('sort'), 'search' => request('search'), 'plant_id' => request('plant_id'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">{{ $farm->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="plantDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Plant: {{ request('plant_id') ? $plants->find(request('plant_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="plantDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($plants as $plant)
                                            <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['plant_id' => $plant->id, 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">{{ $plant->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="actuatorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Actuator: {{ request('actuator_id') ? $actuators->find(request('actuator_id'))->type : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="actuatorDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($actuators as $actuator)
                                            <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['actuator_id' => $actuator->id, 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'status' => request('status')]) }}">{{ ucfirst($actuator->type) }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Status: {{ request('status') ? ucfirst(request('status')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'actuator_id' => request('actuator_id')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['status' => 'pending', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'actuator_id' => request('actuator_id')]) }}">Pending</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['status' => 'completed', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'actuator_id' => request('actuator_id')]) }}">Completed</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['sort' => 'newest', 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.schedules.index', ['sort' => 'oldest', 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.schedules.create') }}" class="btn btn-success">Add New Schedule</a>
                            </div>
                        </div>
                        @if ($schedules->isEmpty())
                            <p class="text-muted">No schedules available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Farm</th>
                                        <th>Plant</th>
                                        <th>Actuator</th>
                                        <th>Schedule Time</th>
                                        <th>Status</th>
                                        <th>Weather Forecast Integration</th>
                                        <th>Priority Zone</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->id }}</td>
                                            <td>{{ $schedule->farm ? $schedule->farm->name : 'N/A' }}</td>
                                            <td>{{ $schedule->plant ? $schedule->plant->name : 'N/A' }}</td>
                                            <td>{{ $schedule->actuator ? ucfirst($schedule->actuator->type) : 'N/A' }}</td>
                                            <td>{{ $schedule->schedule_time->format('d M, Y H:i:s') }}</td>
                                            <td>
                                                <span class="badge {{ $schedule->status === 'pending' ? 'bg-warning' : 'bg-success' }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $schedule->weather_forecast_integration ? 'Yes' : 'No' }}</td>
                                            <td>{{ $schedule->priority_zone ?? 'N/A' }}</td>
                                            <td>{{ $schedule->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
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
                                <p class="text-muted">Showing {{ $schedules->firstItem() }} to {{ $schedules->lastItem() }} of {{ $schedules->total() }} data</p>
                                {{ $schedules->appends(request()->query())->links() }}
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

        .badge.bg-success {
            background-color: #28a745 !important;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
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