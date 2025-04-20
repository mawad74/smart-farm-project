@extends('layouts.dashboard')

@section('title', 'All Farms')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Farms</h5>
                                <p class="text-success">Active Farms</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.farms.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown mx-2">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.farms.index', ['sort' => 'newest', 'search' => request('search')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.farms.index', ['sort' => 'oldest', 'search' => request('search')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.farms.create') }}" class="btn btn-success ms-3">Add New Farm</a>
                            </div>
                        </div>
                        @if ($farms->isEmpty())
                            <p class="text-muted">No farms available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Farm Name</th>
                                        <th>Location</th>
                                        <th>Owner</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($farms as $farm)
                                        <tr>
                                            <td>{{ $farm->id }}</td>
                                            <td>{{ $farm->name }}</td>
                                            <td>{{ $farm->location }}</td>
                                            <td>{{ $farm->user ? $farm->user->name : 'N/A' }}</td>
                                            <td>{{ $farm->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.farms.edit', $farm->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.farms.destroy', $farm->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this farm?');">
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
                                <p class="text-muted">Showing {{ $farms->firstItem() }} to {{ $farms->lastItem() }} of {{ $farms->total() }} data</p>
                                {{ $farms->appends(request()->query())->links() }}
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