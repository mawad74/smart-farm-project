@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Plants')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">My Plants</h5>
                                <p class="text-success">Active Plants</p>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-1 mb-4">
                                <form method="GET" action="{{ route('farmer_manager.plants.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(request('type')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.plants.index', ['sort' => request('sort'), 'search' => request('search')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.plants.index', ['type' => 'vegetable', 'sort' => request('sort'), 'search' => request('search')]) }}">Vegetable</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.plants.index', ['type' => 'fruit', 'sort' => request('sort'), 'search' => request('search')]) }}">Fruit</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.plants.index', ['type' => 'grain', 'sort' => request('sort'), 'search' => request('search')]) }}">Grain</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.plants.index', ['sort' => 'newest', 'search' => request('search'), 'type' => request('type')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.plants.index', ['sort' => 'oldest', 'search' => request('search'), 'type' => request('type')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('farmer_manager.plants.create') }}" class="btn btn-success">Add New Plant</a>
                            </div>
                        </div>
                        @if ($plants->isEmpty())
                            <p class="text-muted">No plants available.</p>
                        @else
                            <div class="row g-3">
                                @foreach ($plants as $plant)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="plant-card" style="min-height: 180px; padding: 16px 12px 10px 12px; display: flex; flex-direction: column; justify-content: space-between;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <div class="desc" style="font-size:0.98rem;font-weight:600;">{{ $plant->name }}</div>
                                                    <div class="main" style="font-size:0.95rem; color:#7a7a7a;">{{ ucfirst($plant->type) }}</div>
                                                </div>
                                                @if($plant->type === 'vegetable')
                                                    <i class="fas fa-carrot plant-icon" style="font-size:1.5rem;color:#e67e22;"></i>
                                                @elseif($plant->type === 'fruit')
                                                    <i class="fas fa-apple-alt plant-icon" style="font-size:1.5rem;color:#e74c3c;"></i>
                                                @elseif($plant->type === 'grain')
                                                    <i class="fas fa-seedling plant-icon" style="font-size:1.5rem;color:#b7950b;"></i>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge plant-badge me-1">Farm: {{ $plant->farm ? $plant->farm->name : 'N/A' }}</span>
                                                <span class="badge plant-badge me-1">Growth: {{ $plant->growth_rate ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge plant-badge me-1">Yield: {{ $plant->yield_prediction ?? 'N/A' }}</span>
                                                <span class="badge plant-badge me-1">{{ $plant->created_at->format('d M, Y') }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge plant-badge me-1 {{ $plant->health_status === 'healthy' ? 'bg-success' : ($plant->health_status === 'diseased' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $plant->health_status)) }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center mt-auto gap-1">
                                                <a href="{{ route('farmer_manager.plants.edit', $plant->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="Edit" style="height:32px;width:32px;padding:0;">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:1rem;"></i>
                                                </a>
                                                <form action="{{ route('farmer_manager.plants.destroy', $plant->id) }}" method="POST" style="display: inline-block;">
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
        .d-flex.gap-1 {
            gap: 5px !important;
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

        .plant-card {
            background: #f3f6d9;
            border-radius: 18px;
            box-shadow: 0 6px 18px 0 rgba(0,0,0,0.13);
            margin-bottom: 14px;
            transition: box-shadow 0.2s;
            border: 1.5px solid #e6eec7;
        }
        .plant-card:hover {
            box-shadow: 0 10px 24px 0 rgba(0,0,0,0.18);
        }
        .plant-card .main {
            color: #3d5c0a;
            font-size: 0.95rem;
            font-weight: bold;
            margin-bottom: 0.1rem;
        }
        .plant-card .desc {
            color: #7a7a7a;
            font-size: 0.98rem;
            font-weight: 600;
        }
        .plant-card .plant-icon {
            margin-left: 8px;
        }
        .plant-badge {
            background: #f8fbe9;
            border: 1px solid #e6eec7;
            color: #3d5c0a;
            font-size: 0.85rem;
            border-radius: 10px;
            margin-bottom: 2px;
        }
        .plant-card .btn-outline-primary, .plant-card .btn-outline-danger {
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .plant-card .btn-outline-primary:hover {
            background: #e6eec7;
            color: #3d5c0a;
        }
        .plant-card .btn-outline-danger:hover {
            background: #ffeaea;
            color: #b30000;
        }
        @media (max-width: 767px) {
            .plant-card {
                padding: 10px 6px 8px 6px;
            }
            .plant-card .main {
                font-size: 0.85rem;
            }
            .plant-card .desc {
                font-size: 0.85rem;
            }
        }
    </style>
@endsection