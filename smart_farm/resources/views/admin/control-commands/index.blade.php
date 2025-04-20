@extends('layouts.dashboard')

@section('title', 'All Control Commands')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Control Commands</h5>
                                <p class="text-success">Active Commands</p>
                            </div>
                            <div class="d-flex align-items-center">
                            <form class="me-3" method="GET" action="{{ route('admin.control-commands.index') }}">
                                <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                            </form> 
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="actuatorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Actuator: {{ request('actuator_id') ? $actuators->find(request('actuator_id'))->type : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="actuatorDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['sort' => request('sort'), 'search' => request('search'), 'user_id' => request('user_id'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($actuators as $actuator)
                                            <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['actuator_id' => $actuator->id, 'sort' => request('sort'), 'search' => request('search'), 'user_id' => request('user_id'), 'status' => request('status')]) }}">{{ ucfirst($actuator->type) }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by User: {{ request('user_id') ? $users->find(request('user_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['sort' => request('sort'), 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($users as $user)
                                            <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['user_id' => $user->id, 'sort' => request('sort'), 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'status' => request('status')]) }}">{{ $user->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Status: {{ request('status') !== null ? (request('status') ? 'Successful' : 'Failed') : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['sort' => request('sort'), 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'user_id' => request('user_id')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['status' => 1, 'sort' => request('sort'), 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'user_id' => request('user_id')]) }}">Successful</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['status' => 0, 'sort' => request('sort'), 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'user_id' => request('user_id')]) }}">Failed</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['sort' => 'newest', 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'user_id' => request('user_id'), 'status' => request('status')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.control-commands.index', ['sort' => 'oldest', 'search' => request('search'), 'actuator_id' => request('actuator_id'), 'user_id' => request('user_id'), 'status' => request('status')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.control-commands.create') }}" class="btn btn-success">Add New Command</a>
                            </div>
                        </div>
                        @if ($commands->isEmpty())
                            <p class="text-muted">No control commands available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Actuator</th>
                                        <th>User</th>
                                        <th>Command Type</th>
                                        <th>Executed At</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($commands as $command)
                                        <tr>
                                            <td>{{ $command->id }}</td>
                                            <td>{{ $command->actuator ? ucfirst($command->actuator->type) : 'N/A' }}</td>
                                            <td>{{ $command->user ? $command->user->name : 'N/A' }}</td>
                                            <td>{{ ucfirst($command->command_type) }}</td>
                                            <td>{{ $command->executed_at->format('d M, Y H:i:s') }}</td>
                                            <td>
                                                <span class="badge {{ $command->status ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $command->status ? 'Successful' : 'Failed' }}
                                                </span>
                                            </td>
                                            <td>{{ $command->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.control-commands.edit', $command->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.control-commands.destroy', $command->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this control command?');">
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