@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Control Commands')
@section('dashboard-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title">My Control Commands</h5>
                            <p class="text-success">Active Commands</p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-1 mb-4">
                            <form method="GET" action="{{ route('farmer_manager.control-commands.index') }}">
                                <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                            </form>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.control-commands.index', ['sort' => 'newest', 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">Newest</a></li>
                                    <li><a class="dropdown-item" href="{{ route('farmer_manager.control-commands.index', ['sort' => 'oldest', 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">Oldest</a></li>
                                </ul>
                            </div>
                            <a href="{{ route('farmer_manager.control-commands.create') }}" class="btn btn-success">Add New Command</a>
                        </div>
                    </div>
                    @if ($commands->isEmpty())
                        <p class="text-muted">No control commands available.</p>
                    @else
                        <div class="row g-3">
                            @foreach ($commands as $command)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="command-card" style="background: #f3f6d9; min-height: 150px; padding: 14px 10px 8px 10px; display: flex; flex-direction: column; justify-content: space-between;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="desc" style="font-size:0.98rem;font-weight:600;">
                                                    <i class="fas fa-terminal text-primary me-1"></i>
                                                    {{ ucfirst($command->command_type) }}
                                                </div>
                                                <div class="main" style="font-size:0.95rem; color:#7a7a7a;">
                                                    Actuator: {{ $command->actuator ? ucfirst($command->actuator->type) : 'N/A' }}
                                                </div>
                                                <div class="main" style="font-size:0.95rem; color:#7a7a7a;">
                                                    User: {{ $command->user ? $command->user->name : 'N/A' }}
                                                </div>
                                            </div>
                                            <i class="fas fa-microchip command-icon" style="font-size:1.5rem;color:#3d5c0a;"></i>
                                        </div>
                                        <div class="mb-2">
                                            <span class="badge command-badge me-1">Executed: {{ $command->executed_at ? $command->executed_at->format('d M, Y H:i') : 'N/A' }}</span>
                                            <span class="badge command-badge me-1">Status: <span class="@if($command->status) bg-success @else bg-danger @endif" style="padding:2px 8px;border-radius:8px;">{{ $command->status ? 'Successful' : 'Failed' }}</span></span>
                                            <span class="badge command-badge me-1">Created: {{ $command->created_at->format('d M, Y') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-end align-items-center mt-auto gap-1">
                                            <a href="{{ route('farmer_manager.control-commands.edit', $command->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center" title="Edit" style="height:32px;width:32px;padding:0;">
                                                <i class="fa-solid fa-pen-to-square" style="font-size:1rem;"></i>
                                            </a>
                                            <form action="{{ route('farmer_manager.control-commands.destroy', $command->id) }}" method="POST" style="display: inline-block;">
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
                            <p class="text-muted">Showing {{ $commands->firstItem() }} to {{ $commands->lastItem() }} of {{ $commands->total() }} data</p>
                            {{ $commands->appends(request()->query())->links() }}
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
.command-card {
    background: #f3f6d9;
    border-radius: 18px;
    box-shadow: 0 6px 18px 0 rgba(0,0,0,0.13);
    margin-bottom: 14px;
    transition: box-shadow 0.2s;
    border: 1.5px solid #e6eec7;
}
.command-card:hover {
    box-shadow: 0 10px 24px 0 rgba(0,0,0,0.18);
}
.command-card .main {
    color: #3d5c0a;
    font-size: 0.95rem;
    font-weight: bold;
    margin-bottom: 0.1rem;
}
.command-card .desc {
    color: #7a7a7a;
    font-size: 0.98rem;
    font-weight: 600;
}
.command-card .command-icon {
    margin-left: 8px;
}
.command-badge {
    background: #f8fbe9;
    border: 1px solid #e6eec7;
    color: #3d5c0a;
    font-size: 0.85rem;
    border-radius: 10px;
    margin-bottom: 2px;
}
.command-card .btn-outline-primary, .command-card .btn-outline-danger {
    border-radius: 8px;
    font-size: 0.95rem;
}
.command-card .btn-outline-primary:hover {
    background: #e6eec7;
    color: #3d5c0a;
}
.command-card .btn-outline-danger:hover {
    background: #ffeaea;
    color: #b30000;
}
@media (max-width: 767px) {
    .command-card {
        padding: 10px 6px 8px 6px;
    }
    .command-card .main {
        font-size: 0.85rem;
    }
    .command-card .desc {
        font-size: 0.85rem;
    }
}
</style>
@endsection