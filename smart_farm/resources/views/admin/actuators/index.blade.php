@extends('layouts.dashboard')

@section('title', 'All Actuators')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Actuators</h5>
                                <p class="text-success">Active Actuators</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.actuators.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="farmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Farm: {{ request('farm_id') ? $farms->find(request('farm_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="farmDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['sort' => request('sort'), 'search' => request('search'), 'plant_id' => request('plant_id'), 'type' => request('type'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($farms as $farm)
                                            <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['farm_id' => $farm->id, 'sort' => request('sort'), 'search' => request('search'), 'plant_id' => request('plant_id'), 'type' => request('type'), 'status' => request('status')]) }}">{{ $farm->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="plantDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Plant: {{ request('plant_id') ? $plants->find(request('plant_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="plantDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($plants as $plant)
                                            <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['plant_id' => $plant->id, 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type'), 'status' => request('status')]) }}">{{ $plant->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(request('type')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'status' => request('status')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['type' => 'irrigation_pump', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'status' => request('status')]) }}">Irrigation Pump</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['type' => 'ventilation', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'status' => request('status')]) }}">Ventilation</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['type' => 'lighting', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'status' => request('status')]) }}">Lighting</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Status: {{ request('status') ? ucfirst(request('status')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'type' => request('type')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['status' => 'active', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'type' => request('type')]) }}">Active</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['status' => 'inactive', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'type' => request('type')]) }}">Inactive</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['status' => 'faulty', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'type' => request('type')]) }}">Faulty</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['sort' => 'newest', 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'type' => request('type'), 'status' => request('status')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.actuators.index', ['sort' => 'oldest', 'search' => request('search'), 'farm_id' => request('farm_id'), 'plant_id' => request('plant_id'), 'type' => request('type'), 'status' => request('status')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.actuators.create') }}" class="btn btn-success">Add New Actuator</a>
                            </div>
                        </div>
                        @if ($actuators->isEmpty())
                            <p class="text-muted">No actuators available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Farm</th>
                                        <th>Plant</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action Type</th>
                                        <th>Last Triggered At</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($actuators as $actuator)
                                        <tr>
                                            <td>{{ $actuator->id }}</td>
                                            <td>{{ $actuator->farm ? $actuator->farm->name : 'N/A' }}</td>
                                            <td>{{ $actuator->plant ? $actuator->plant->name : 'N/A' }}</td>
                                            <td>{{ ucfirst($actuator->type) }}</td>
                                            <td>
                                                <span class="badge {{ $actuator->status === 'active' ? 'bg-success' : ($actuator->status === 'inactive' ? 'bg-secondary' : 'bg-danger') }}">
                                                    {{ ucfirst($actuator->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $actuator->action_type ?? 'N/A' }}</td>
                                            <td>{{ $actuator->last_triggered_at ? $actuator->last_triggered_at->format('d M, Y H:i:s') : 'N/A' }}</td>
                                            <td>{{ $actuator->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.actuators.edit', $actuator->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.actuators.destroy', $actuator->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this actuator?');">
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
                                <p class="text-muted">Showing {{ $actuators->firstItem() }} to {{ $actuators->lastItem() }} of {{ $actuators->total() }} data</p>
                                {{ $actuators->appends(request()->query())->links() }}
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

        .badge.bg-secondary {
            background-color: #6c757d !important;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
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