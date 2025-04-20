@extends('layouts.dashboard')

@section('title', 'All Financial Records')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Financial Records</h5>
                                <p class="text-success">Financial Reports</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.financial-records.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="farmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Farm: {{ request('farm_id') ? $farms->find(request('farm_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="farmDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.financial-records.index', ['sort' => request('sort'), 'search' => request('search'), 'type' => request('type')]) }}">All</a></li>
                                        @foreach ($farms as $farm)
                                            <li><a class="dropdown-item" href="{{ route('admin.financial-records.index', ['farm_id' => $farm->id, 'sort' => request('sort'), 'search' => request('search'), 'type' => request('type')]) }}">{{ $farm->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="typeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Type: {{ request('type') ? ucfirst(str_replace('_', ' ', request('type'))) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="typeDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.financial-records.index', ['sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id')]) }}">All</a></li>
                                        @foreach ($types as $type)
                                            <li><a class="dropdown-item" href="{{ route('admin.financial-records.index', ['type' => $type, 'sort' => request('sort'), 'search' => request('search'), 'farm_id' => request('farm_id')]) }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.financial-records.index', ['sort' => 'newest', 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.financial-records.index', ['sort' => 'oldest', 'search' => request('search'), 'farm_id' => request('farm_id'), 'type' => request('type')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.financial-records.create') }}" class="btn btn-success">Add New Record</a>
                            </div>
                        </div>
                        @if ($records->isEmpty())
                            <p class="text-muted">No financial records available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Farm</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Description</th>
                                        <th>Timestamp</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($records as $record)
                                        <tr>
                                            <td>{{ $record->id }}</td>
                                            <td>{{ $record->farm ? $record->farm->name : 'N/A' }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $record->type)) }}</td>
                                            <td>{{ number_format($record->value, 2) }}</td>
                                            <td>{{ $record->description ?? 'N/A' }}</td>
                                            <td>
                                                @if ($record->timestamp instanceof \Carbon\Carbon)
                                                    {{ $record->timestamp->format('d M, Y H:i:s') }}
                                                @else
                                                    {{ $record->timestamp ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td>{{ $record->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.financial-records.edit', $record->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.financial-records.destroy', $record->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this financial record?');">
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
                                <p class="text-muted">Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} data</p>
                                {{ $records->appends(request()->query())->links() }}
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