@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Sensors')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">My Sensors</h5>
                                <p class="text-success">Active Sensors</p>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-1 mb-4">
                                <form method="GET" action="{{ route('farmer_manager.sensors.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(request('type')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.sensors.index', ['sort' => request('sort'), 'search' => request('search')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.sensors.index', ['type' => 'temperature', 'sort' => request('sort'), 'search' => request('search')]) }}">Temperature</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.sensors.index', ['type' => 'humidity', 'sort' => request('sort'), 'search' => request('search')]) }}">Humidity</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.sensors.index', ['type' => 'soil', 'sort' => request('sort'), 'search' => request('search')]) }}">Soil</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.sensors.index', ['type' => 'light', 'sort' => request('sort'), 'search' => request('search')]) }}">Light</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.sensors.index', ['sort' => 'newest', 'search' => request('search'), 'type' => request('type')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.sensors.index', ['sort' => 'oldest', 'search' => request('search'), 'type' => request('type')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('farmer_manager.sensors.create') }}" class="btn btn-success">Add New Sensor</a>
                            </div>
                        </div>
                        @if ($sensors->isEmpty())
                            <p class="text-muted">No sensors available.</p>
                        @else
                            <div class="row g-3">
                                @foreach ($sensors as $sensor)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="sensor-card" style="min-height: 150px; padding: 14px 10px 8px 10px; display: flex; flex-direction: column; justify-content: space-between;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <div class="desc" style="font-size:0.98rem;font-weight:600;">{{ $sensor->name }}</div>
                                                    <div class="main" style="font-size:0.95rem; color:#7a7a7a;">{{ ucfirst($sensor->type) }}</div>
                                                </div>
                                                @if($sensor->type === 'temperature')
                                                    <i class="fas fa-thermometer-half sensor-icon" style="font-size:1.5rem;color:#e67e22;"></i>
                                                @elseif($sensor->type === 'humidity')
                                                    <i class="fas fa-tint sensor-icon" style="font-size:1.5rem;color:#3498db;"></i>
                                                @elseif($sensor->type === 'soil')
                                                    <i class="fas fa-seedling sensor-icon" style="font-size:1.5rem;color:#7c8a3a;"></i>
                                                @elseif($sensor->type === 'light')
                                                    <i class="fas fa-sun sensor-icon" style="font-size:1.5rem;color:#f1c40f;"></i>
                                                @else
                                                    <i class="fas fa-microchip sensor-icon" style="font-size:1.5rem;color:#888;"></i>
                                                @endif
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge sensor-badge me-1">Farm: {{ $sensor->farm ? $sensor->farm->name : 'N/A' }}</span>
                                                <span class="badge sensor-badge me-1">Status: {{ ucfirst($sensor->status) }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge sensor-badge me-1">Created: {{ $sensor->created_at->format('d M, Y') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center mt-auto gap-1">
                                                <a href="{{ route('farmer_manager.sensors.edit', $sensor->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="Edit" style="height:32px;width:32px;padding:0;">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:1rem;"></i>
                                                </a>
                                                <form action="{{ route('farmer_manager.sensors.destroy', $sensor->id) }}" method="POST" style="display: inline-block;">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .d-flex.gap-1 {
            gap: 5px !important;
        }
        .sensor-card {
            background: #f3f6d9;
            border-radius: 18px;
            box-shadow: 0 6px 18px 0 rgba(0,0,0,0.13);
            margin-bottom: 14px;
            transition: box-shadow 0.2s;
            border: 1.5px solid #e6eec7;
        }
        .sensor-card:hover {
            box-shadow: 0 10px 24px 0 rgba(0,0,0,0.18);
        }
        .sensor-card .main {
            color: #3d5c0a;
            font-size: 0.95rem;
            font-weight: bold;
            margin-bottom: 0.1rem;
        }
        .sensor-card .desc {
            color: #7a7a7a;
            font-size: 0.98rem;
            font-weight: 600;
        }
        .sensor-card .sensor-icon {
            margin-left: 8px;
        }
        .sensor-badge {
            background: #f8fbe9;
            border: 1px solid #e6eec7;
            color: #3d5c0a;
            font-size: 0.85rem;
            border-radius: 10px;
            margin-bottom: 2px;
        }
        .sensor-card .btn-outline-primary, .sensor-card .btn-outline-danger {
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .sensor-card .btn-outline-primary:hover {
            background: #e6eec7;
            color: #3d5c0a;
        }
        .sensor-card .btn-outline-danger:hover {
            background: #ffeaea;
            color: #b30000;
        }
        @media (max-width: 767px) {
            .sensor-card {
                padding: 10px 6px 8px 6px;
            }
            .sensor-card .main {
                font-size: 0.85rem;
            }
            .sensor-card .desc {
                font-size: 0.85rem;
            }
        }
    </style>
@endsection