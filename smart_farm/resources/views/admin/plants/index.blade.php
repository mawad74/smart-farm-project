@extends('layouts.dashboard')

@section('title', 'All Plants')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Plants</h5>
                                <p class="text-success">Active Plants</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.plants.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(request('type')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'health_status' => request('health_status')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['type' => 'vegetable', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'health_status' => request('health_status')]) }}">Vegetable</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['type' => 'fruit', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'health_status' => request('health_status')]) }}">Fruit</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['type' => 'grain', 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id'), 'health_status' => request('health_status')]) }}">Grain</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="farmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Farm: {{ request('farm_id') ? $farms->find(request('farm_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="farmDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'health_status' => request('health_status')]) }}">All</a></li>
                                        @foreach ($farms as $farm)
                                            <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['farm_id' => $farm->id, 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'health_status' => request('health_status')]) }}">{{ $farm->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="healthStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Health Status: {{ request('health_status') ? ucfirst(str_replace('_', ' ', request('health_status'))) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="healthStatusDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['health_status' => 'healthy', 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id')]) }}">Healthy</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['health_status' => 'diseased', 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id')]) }}">Diseased</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['health_status' => 'needs_attention', 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id')]) }}">Needs Attention</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['sort' => 'newest', 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id'), 'health_status' => request('health_status')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.plants.index', ['sort' => 'oldest', 'search' => request('search'), 'type' => request('type'), 'farm_id' => request('farm_id'), 'health_status' => request('health_status')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.plants.create') }}" class="btn btn-success">Add New Plant</a>
                            </div>
                        </div>
                        @if ($plants->isEmpty())
                            <p class="text-muted">No plants available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Farm</th>
                                        <th>Health Status</th>
                                        <th>Growth Rate</th>
                                        <th>Yield Prediction</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($plants as $plant)
                                        <tr>
                                            <td>{{ $plant->id }}</td>
                                            <td>{{ $plant->name }}</td>
                                            <td>{{ ucfirst($plant->type) }}</td>
                                            <td>{{ $plant->farm ? $plant->farm->name : 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $plant->health_status === 'healthy' ? 'bg-success' : ($plant->health_status === 'diseased' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $plant->health_status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $plant->growth_rate ?? 'N/A' }}</td>
                                            <td>{{ $plant->yield_prediction ?? 'N/A' }}</td>
                                            <td>{{ $plant->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.plants.edit', $plant->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.plants.destroy', $plant->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this plant?');">
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
                                <p class="text-muted">Showing {{ $plants->firstItem() }} to {{ $plants->lastItem() }} of {{ $plants->total() }} data</p>
                                {{ $plants->appends(request()->query())->links() }}
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