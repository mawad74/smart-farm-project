@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Alerts')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">My Alerts</h5>
                                <p class="text-success">Latest Alerts</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('farmer_manager.alerts.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown mx-2">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.alerts.index', ['sort' => 'newest', 'search' => request('search')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('farmer_manager.alerts.index', ['sort' => 'oldest', 'search' => request('search')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('farmer_manager.alerts.create') }}" class="btn btn-success ms-3">Add New Alert</a>
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
                                        <th>Severity</th>
                                        <th>Date</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alerts as $alert)
                                        <tr>
                                            <td>{{ $alert->id }}</td>
                                            <td>{{ $alert->message }}</td>
                                            <td>{{ $alert->severity }}</td>
                                            <td>{{ $alert->date }}</td>
                                            <td>{{ $alert->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('farmer_manager.alerts.edit', $alert->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('farmer_manager.alerts.destroy', $alert->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this alert?');">
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