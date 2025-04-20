@extends('layouts.dashboard')

@section('title', 'All Subscriptions')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Notifications</h5>
                        @if ($expiredSubscriptions->isNotEmpty())
                            <div class="alert alert-danger">
                                <strong>Expired Subscriptions:</strong>
                                <ul>
                                    @foreach ($expiredSubscriptions as $expired)
                                        <li>Subscription for {{ $expired->user->name }} ({{ $expired->user->email }}) expired on {{ $expired->end_date->format('d M, Y') }}.</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($expiringSoonSubscriptions->isNotEmpty())
                            <div class="alert alert-warning">
                                <strong>Subscriptions Expiring Soon (within 3 days):</strong>
                                <ul>
                                    @foreach ($expiringSoonSubscriptions as $expiring)
                                        <li>Subscription for {{ $expiring->user->name }} ({{ $expiring->user->email }}) will expire on {{ $expiring->end_date->format('d M, Y') }}.</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($expiredSubscriptions->isEmpty() && $expiringSoonSubscriptions->isEmpty())
                            <p class="text-muted">No subscription notifications at the moment.</p>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">All Subscriptions</h5>
                                <p class="text-success">Active Subscriptions</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <form class="me-3" method="GET" action="{{ route('admin.subscriptions.index') }}">
                                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                                </form>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by User: {{ request('user_id') ? $users->find(request('user_id'))->name : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['sort' => request('sort'), 'search' => request('search'), 'status' => request('status')]) }}">All</a></li>
                                        @foreach ($users as $user)
                                            <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['user_id' => $user->id, 'sort' => request('sort'), 'search' => request('search'), 'status' => request('status')]) }}">{{ $user->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter by Status: {{ request('status') ? ucfirst(request('status')) : 'All' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['sort' => request('sort'), 'search' => request('search'), 'user_id' => request('user_id')]) }}">All</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['status' => 'active', 'sort' => request('sort'), 'search' => request('search'), 'user_id' => request('user_id')]) }}">Active</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['status' => 'expired', 'sort' => request('sort'), 'search' => request('search'), 'user_id' => request('user_id')]) }}">Expired</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['status' => 'canceled', 'sort' => request('sort'), 'search' => request('search'), 'user_id' => request('user_id')]) }}">Canceled</a></li>
                                    </ul>
                                </div>
                                <div class="dropdown me-3">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: {{ request('sort', 'newest') === 'newest' ? 'Newest' : 'Oldest' }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['sort' => 'newest', 'search' => request('search'), 'user_id' => request('user_id'), 'status' => request('status')]) }}">Newest</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.subscriptions.index', ['sort' => 'oldest', 'search' => request('search'), 'user_id' => request('user_id'), 'status' => request('status')]) }}">Oldest</a></li>
                                    </ul>
                                </div>
                                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-success">Add New Subscription</a>
                            </div>
                        </div>
                        @if ($subscriptions->isEmpty())
                            <p class="text-muted">No subscriptions available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subscriptions as $subscription)
                                        <tr class="{{ $subscription->end_date && \Carbon\Carbon::parse($subscription->end_date)->isPast() && $subscription->status === 'active' ? 'table-danger' : ($subscription->end_date && \Carbon\Carbon::parse($subscription->end_date)->isBetween(\Carbon\Carbon::today(), \Carbon\Carbon::today()->addDays(3)) && $subscription->status === 'active' ? 'table-warning' : '') }}">
                                            <td>{{ $subscription->id }}</td>
                                            <td>{{ $subscription->user ? $subscription->user->name : 'N/A' }}</td>
                                            <td>{{ $subscription->start_date ? $subscription->start_date->format('d M, Y') : 'N/A' }}</td>
                                            <td>{{ $subscription->end_date ? $subscription->end_date->format('d M, Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $subscription->status === 'active' ? 'bg-success' : ($subscription->status === 'expired' ? 'bg-danger' : 'bg-warning') }}">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $subscription->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.subscriptions.destroy', $subscription->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this subscription?');">
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
                                <p class="text-muted">Showing {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} of {{ $subscriptions->total() }} data</p>
                                {{ $subscriptions->appends(request()->query())->links() }}
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

        .badge.bg-warning {
            background-color: #ffc107 !important;
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

        .table-danger {
            background-color: #f8d7da !important;
        }

        .table-warning {
            background-color: #fff3cd !important;
        }
    </style>
@endsection