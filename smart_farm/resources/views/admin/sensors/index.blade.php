@extends('layouts.dashboard')

@section('title', 'All Sensors')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Sensors</h5>
                                <p class="text-success">Active Sensors</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.sensors.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="farmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Farm: {{ request('farm_id') ? $farms->find(request('farm_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="farmDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($farms as $farm)
                                            <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['farm_id' => $farm->id, 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'status' => request('status')]) }}">{{ $farm->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(request('type')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['type' => 'temperature', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">Temperature</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['type' => 'humidity', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">Humidity</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['type' => 'soil_moisture', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">Soil Moisture</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['type' => 'ph', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">pH</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['type' => 'nutrient', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">Nutrient</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Status: {{ request('status') ? ucfirst(request('status')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['status' => 'active', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type')]) }}">Active</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['status' => 'inactive', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type')]) }}">Inactive</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['status' => 'faulty', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type')]) }}">Faulty</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['sort' => 'newest', 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type'), 'status' => request('status')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.sensors.index', ['sort' => 'oldest', 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type'), 'status' => request('status')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.sensors.create') }}" class="btn btn-success">Add New Sensor</a>
                            </div>
                        </div>
                        @if ($sensors->isEmpty())
                            <p class="text-muted">No sensors available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Device ID</th>
                                        <th>Name</th>
                                        <th>Farm</th>
                                        <th>Plant</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Location</th>
                                        <th>Light Intensity</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sensors as $sensor)
                                        <tr>
                                            <td>{{ $sensor->id }}</td>
                                            <td>{{ $sensor->device_id ?? 'N/A' }}</td>
                                            <td>{{ $sensor->name ?? 'N/A' }}</td>
                                            <td>{{ $sensor->farm ? $sensor->farm->name : 'N/A' }}</td>
                                            <td>{{ $sensor->plant ? $sensor->plant->name : 'N/A' }}</td>
                                            <td>{{ ucfirst($sensor->type) }}</td>
                                            <td>
                                                <span class="badge {{ $sensor->status === 'active' ? 'bg-success' : ($sensor->status === 'inactive' ? 'bg-secondary' : 'bg-danger') }}">
                                                    {{ ucfirst($sensor->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $sensor->location ?? 'N/A' }}</td>
                                            <td>{{ $sensor->light_intensity ?? 'N/A' }}</td>
                                            <td>{{ $sensor->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.sensors.edit', $sensor->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.sensors.readings', $sensor->id) }}" class="btn btn-sm btn-outline-info me-1" title="View Readings">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.sensors.destroy', $sensor->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this sensor?');">
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
                                <p class="text-muted">Showing {{ $sensors->firstItem() }} to {{ $sensors->lastItem() }} of {{ $sensors->total() }} data</p>
                                {{ $sensors->appends(request()->query())->links() }}
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

        .btn-outline-primary, .btn-outline-danger, .btn-outline-info {
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