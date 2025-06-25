@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Actuators')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">My Actuators</h5>
                                <p class="text-success">Active Actuators</p>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-1 mb-4">
                                <form method="GET" action="{{ route('farmer_manager.actuators.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(request('type')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.actuators.index', ['sort' => request('sort'), 'search' => request('search')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.actuators.index', ['type' => 'irrigation', 'sort' => request('sort'), 'search' => request('search')]) }}">Irrigation</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.actuators.index', ['type' => 'fan', 'sort' => request('sort'), 'search' => request('search')]) }}">Fan</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.actuators.index', ['type' => 'light', 'sort' => request('sort'), 'search' => request('search')]) }}">Light</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.actuators.index', ['type' => 'fertilizer', 'sort' => request('sort'), 'search' => request('search')]) }}">Fertilizer</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.actuators.index', ['sort' => 'newest', 'search' => request('search'), 'type' => request('type')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.actuators.index', ['sort' => 'oldest', 'search' => request('search'), 'type' => request('type')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('farmer_manager.actuators.create') }}" class="btn btn-success">Add New Actuator</a>
                            </div>
                        </div>
                        @if ($actuators->isEmpty())
                            <p class="text-muted">No actuators available.</p>
                        @else
                            <div class="row g-3">
                                @foreach ($actuators as $actuator)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="actuator-card" style="min-height: 180px; padding: 14px 10px 8px 10px; display: flex; flex-direction: column; justify-content: space-between;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <div class="desc" style="font-size:0.98rem;font-weight:600;">{{ $actuator->name }}</div>
                                                    <div class="main" style="font-size:0.95rem; color:#7a7a7a;">{{ ucfirst($actuator->type) }}</div>
                                                </div>
                                                @if($actuator->type === 'irrigation')
                                                    <i class="fas fa-water actuator-icon" style="font-size:1.5rem;color:#3498db;"></i>
                                                @elseif($actuator->type === 'fan')
                                                    <i class="fas fa-wind actuator-icon" style="font-size:1.5rem;color:#7c8a3a;"></i>
                                                @elseif($actuator->type === 'light')
                                                    <i class="fas fa-lightbulb actuator-icon" style="font-size:1.5rem;color:#f1c40f;"></i>
                                                @elseif($actuator->type === 'fertilizer')
                                                    <i class="fas fa-flask actuator-icon" style="font-size:1.5rem;color:#27ae60;"></i>
                                                @else
                                                    <i class="fas fa-cogs actuator-icon" style="font-size:1.5rem;color:#888;"></i>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge actuator-badge me-1">Plant: {{ $actuator->plant ? $actuator->plant->name : 'N/A' }}</span>
                                                <span class="badge actuator-badge me-1">Farm: {{ $actuator->farm ? $actuator->farm->name : 'N/A' }}</span>
                                                <span class="badge actuator-badge me-1">Status:
                                                    <span style="display:inline-block;width:6px;"></span>
                                                    <span class="@if($actuator->status === 'active') bg-success @elseif($actuator->status === 'inactive') bg-danger @else bg-warning @endif" style="padding:2px 8px;border-radius:8px;">
                                                        {{ ucfirst($actuator->status) }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge actuator-badge me-1">Action Type: {{ $actuator->action_type ?? 'N/A' }}</span>
                                                <span class="badge actuator-badge me-1">Created: {{ $actuator->created_at->format('d M, Y') }}</span>
                                                <span class="badge actuator-badge me-1">Updated: {{ $actuator->updated_at ? $actuator->updated_at->format('d M, Y') : 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center mt-auto gap-1">
                                                <a href="{{ route('farmer_manager.actuators.edit', $actuator->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="Edit" style="height:32px;width:32px;padding:0;">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:1rem;"></i>
                                                </a>
                                                <form action="{{ route('farmer_manager.actuators.destroy', $actuator->id) }}" method="POST" style="display: inline-block;">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .bg-success { background-color: #28a745 !important; color: #fff !important; }
        .bg-danger { background-color: #dc3545 !important; color: #fff !important; }
        .bg-warning { background-color: #ffc107 !important; color: #333 !important; }
        .d-flex.gap-1 {
            gap: 5px !important;
        }
        .actuator-card {
            background: #f3f6d9;
            border-radius: 18px;
            box-shadow: 0 6px 18px 0 rgba(0,0,0,0.13);
            margin-bottom: 14px;
            transition: box-shadow 0.2s;
            border: 1.5px solid #e6eec7;
        }
        .actuator-card:hover {
            box-shadow: 0 10px 24px 0 rgba(0,0,0,0.18);
        }
        .actuator-card .main {
            color: #3d5c0a;
            font-size: 0.95rem;
            font-weight: bold;
            margin-bottom: 0.1rem;
        }
        .actuator-card .desc {
            color: #7a7a7a;
            font-size: 0.98rem;
            font-weight: 600;
        }
        .actuator-card .actuator-icon {
            margin-left: 8px;
        }
        .actuator-badge {
            background: #f8fbe9;
            border: 1px solid #e6eec7;
            color: #3d5c0a;
            font-size: 0.85rem;
            border-radius: 10px;
            margin-bottom: 2px;
        }
        .actuator-card .btn-outline-primary, .actuator-card .btn-outline-danger {
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .actuator-card .btn-outline-primary:hover {
            background: #e6eec7;
            color: #3d5c0a;
        }
        .actuator-card .btn-outline-danger:hover {
            background: #ffeaea;
            color: #b30000;
        }
        @media (max-width: 767px) {
            .actuator-card {
                padding: 10px 6px 8px 6px;
            }
            .actuator-card .main {
                font-size: 0.85rem;
            }
            .actuator-card .desc {
                font-size: 0.85rem;
            }
        }
    </style>
@endsection