@extends('layouts.dashboard')

@section('title', 'All Reports')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Reports</h5>
                                <p class="text-success">System Reports</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.reports.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(str_replace('_', ' ', request('type'))) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index', ['sort' => request('sort'), 'search' => request('search')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index', ['type' => 'system_performance', 'sort' => request('sort'), 'search' => request('search')]) }}">System Performance</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index', ['type' => 'alert_history', 'sort' => request('sort'), 'search' => request('search')]) }}">Alert History</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index', ['type' => 'environmental_conditions', 'sort' => request('sort'), 'search' => request('search')]) }}">Environmental Conditions</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index', ['type' => 'resource_usage', 'sort' => request('sort'), 'search' => request('search')]) }}">Resource Usage</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index', ['type' => 'crop_health', 'sort' => request('sort'), 'search' => request('search')]) }}">Crop Health</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index', ['sort' => 'newest', 'search' => request('search'), 'type' => request('type')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.index', ['sort' => 'oldest', 'search' => request('search'), 'type' => request('type')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.reports.create') }}" class="btn btn-success">Add New Report</a>
                            </div>
                        </div>
                        @if ($reports->isEmpty())
                            <p class="text-muted">No reports available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Farm</th>
                                        <th>User</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reports as $report)
                                        <tr>
                                            <td>{{ $report->id }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $report->type)) }}</td>
                                            <td>{{ $report->farm ? $report->farm->name : 'N/A' }}</td>
                                            <td>{{ $report->user ? $report->user->name : 'N/A' }}</td>
                                            <td>{{ $report->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-outline-info me-1" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.reports.edit', $report->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.reports.export', $report->id) }}" class="btn btn-sm btn-outline-success me-1" title="Export to PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this report?');">
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
                                <p class="text-muted">Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} data</p>
                                {{ $reports->appends(request()->query())->links() }}
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

        .btn-outline-info, .btn-outline-primary, .btn-outline-success, .btn-outline-danger {
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