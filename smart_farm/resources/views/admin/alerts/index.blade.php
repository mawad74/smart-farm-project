@extends('layouts.dashboard')

@section('title', 'All Alerts')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Alerts</h5>
                                <p class="text-success">Active Alerts</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.alerts.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(str_replace('_', ' ', request('type'))) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['type' => 'low_soil_moisture', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">Low Soil Moisture</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['type' => 'high_temperature', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">High Temperature</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['type' => 'equipment_failure', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">Equipment Failure</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="farmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Farm: {{ request('farm_id') ? $farms->find(request('farm_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="farmDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($farms as $farm)
                                            <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['farm_id' => $farm->id, 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'status' => request('status')]) }}">{{ $farm->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Status: {{ request('status') ? ucfirst(request('status')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['status' => 'pending', 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id')]) }}">Pending</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['status' => 'dismissed', 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id')]) }}">Dismissed</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['status' => 'resolved', 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id')]) }}">Resolved</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['sort' => 'newest', 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.alerts.index', ['sort' => 'oldest', 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id'), 'status' => request('status')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @if ($alerts->isEmpty())
                            <p class="text-muted">No alerts available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Message</th>
                                        <th>Type</th>
                                        <th>Farm</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alerts as $alert)
                                        <tr>
                                            <td>{{ $alert->id }}</td>
                                            <td>{{ $alert->message }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</td>
                                            <td>{{ $alert->farm ? $alert->farm->name : 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $alert->status === 'pending' ? 'bg-warning' : ($alert->status === 'dismissed' ? 'bg-secondary' : 'bg-success') }}">
                                                    {{ ucfirst($alert->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $alert->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.alerts.edit', $alert->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.alerts.destroy', $alert->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this alert?');">
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
                                <p class="text-muted">Showing {{ $alerts->firstItem() }} to {{ $alerts->lastItem() }} of {{ $alerts->total() }} data</p>
                                {{ $alerts->appends(request()->query())->links() }}
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

        .table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
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