@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Disease Detections')
@section('dashboard-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title">My Disease Detections</h5>
                            <p class="text-success">Disease Reports</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-1 mb-4">
                            <form method="GET" action="{{ route('farmer_manager.disease-detections.index') }}">
                                <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                            </form>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="plantDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filter by Plant: {{ request('plant_id') ? $plants->find(request('plant_id'))->name : 'All' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="plantDropdown">
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.disease-detections.index', ['sort' => request('sort'), 'search' => request('search'), 'disease_name' => request('disease_name')]) }}">All</a></li>
                                    @foreach ($plants as $plant)
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.disease-detections.index', ['plant_id' => $plant->id, 'sort' => request('sort'), 'search' => request('search'), 'disease_name' => request('disease_name')]) }}">{{ $plant->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="diseaseDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filter by Disease: {{ request('disease_name') ? ucfirst(request('disease_name')) : 'All' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="diseaseDropdown">
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.disease-detections.index', ['sort' => request('sort'), 'search' => request('search'), 'plant_id' => request('plant_id')]) }}">All</a></li>
                                    @foreach ($diseaseNames as $disease)
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.disease-detections.index', ['disease_name' => $disease, 'sort' => request('sort'), 'search' => request('search'), 'plant_id' => request('plant_id')]) }}">{{ ucfirst($disease) }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.disease-detections.index', ['sort' => 'newest', 'search' => request('search'), 'plant_id' => request('plant_id'), 'disease_name' => request('disease_name')]) }}">Newest</a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.disease-detections.index', ['sort' => 'oldest', 'search' => request('search'), 'plant_id' => request('plant_id'), 'disease_name' => request('disease_name')]) }}">Oldest</a></li>
                                </ul>
                            </div>
                            <a href="{{ route('farmer_manager.disease-detections.create') }}" class="btn btn-success">Add New Detection</a>
                        </div>
                    </div>
                    @if ($detections->isEmpty())
                        <p class="text-muted">No disease detections available.</p>
                    @else
                        <div class="row g-3">
                            @foreach ($detections as $detection)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="disease-card" style="min-height: 150px; padding: 14px 10px 8px 10px; display: flex; flex-direction: column; justify-content: space-between;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="desc" style="font-size:0.98rem;font-weight:600;">
                                                    <i class="fas fa-bug text-danger me-1"></i>
                                                    {{ ucfirst($detection->disease_name) }}
                                                </div>
                                                <div class="main" style="font-size:0.95rem; color:#7a7a7a;">
                                                    {{ $detection->plant ? $detection->plant->name : 'N/A' }}
                                                </div>
                                            </div>
                                            <i class="fas fa-notes-medical disease-icon" style="font-size:1.5rem;color:#b30000;"></i>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge disease-badge me-1">Confidence: {{ number_format($detection->confidence * 100, 2) }}%</span>
                                            <span class="badge disease-badge me-1">Date: {{ $detection->created_at->format('d M, Y') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-end align-items-center mt-auto gap-1">
                                            <a href="{{ route('farmer_manager.disease-detections.edit', $detection->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="Edit" style="height:32px;width:32px;padding:0;">
                                                <i class="fa-solid fa-pen-to-square" style="font-size:1rem;"></i>
                                            </a>
                                            <form action="{{ route('farmer_manager.disease-detections.destroy', $detection->id) }}" method="POST" style="display: inline-block;">
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
                            <p class="text-muted">Showing {{ $detections->firstItem() }} to {{ $detections->lastItem() }} of {{ $detections->total() }} data</p>
                            {{ $detections->appends(request()->query())->links() }}
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
.disease-card {
    background: #fbe9e9;
    border-radius: 18px;
    box-shadow: 0 6px 18px 0 rgba(0,0,0,0.13);
    margin-bottom: 14px;
    transition: box-shadow 0.2s;
    border: 1.5px solid #f5c6cb;
}
.disease-card:hover {
    box-shadow: 0 10px 24px 0 rgba(0,0,0,0.18);
}
.disease-card .main {
    color: #b30000;
    font-size: 0.95rem;
    font-weight: bold;
    margin-bottom: 0.1rem;
}
.disease-card .desc {
    color: #7a7a7a;
    font-size: 0.98rem;
    font-weight: 600;
}
.disease-card .disease-icon {
    margin-left: 8px;
}
.disease-badge {
    background: #fff0f0;
    border: 1px solid #f5c6cb;
    color: #b30000;
    font-size: 0.85rem;
    border-radius: 10px;
    margin-bottom: 2px;
}
.disease-card .btn-outline-primary, .disease-card .btn-outline-danger {
    border-radius: 8px;
    font-size: 0.95rem;
}
.disease-card .btn-outline-primary:hover {
    background: #f5c6cb;
    color: #b30000;
}
.disease-card .btn-outline-danger:hover {
    background: #ffeaea;
    color: #b30000;
}
@media (max-width: 767px) {
    .disease-card {
        padding: 10px 6px 8px 6px;
    }
    .disease-card .main {
        font-size: 0.85rem;
    }
    .disease-card .desc {
        font-size: 0.85rem;
    }
}
</style>
@endsection