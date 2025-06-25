@extends('layouts.dashboard')

@section('title', 'Logs')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Activity Logs</h5>
                        @if ($logs->isEmpty())
                            <p class="text-muted">No logs available. 
                                <strong>Please try performing actions (e.g., login, logout, or CRUD operations) and check the logs file at <code>storage/logs/laravel.log</code> for debugging.</strong>
                            </p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Farm</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                        <th>Message</th>
                                        <th>Timestamp</th>
                                        <th>Logout Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($logs as $log)
                                        <tr>
                                            <td>{{ $log->user ? $log->user->name : 'N/A' }}</td>
                                            <td>{{ $log->farm ? $log->farm->name : 'N/A' }}</td>
                                            <td>{{ $log->action }}</td>
                                            <td>
                                                <span class="badge {{ $log->status == 'success' ? 'bg-success' : ($log->status == 'failed' ? 'bg-danger' : 'bg-info') }}">
                                                    {{ $log->status }}
                                                </span>
                                            </td>
                                            <td>{{ $log->message ?? 'N/A' }}</td>
                                            <td>{{ $log->timestamp->toDateTimeString() }}</td>
                                            <td>{{ $log->logout_time ? $log->logout_time->toDateTimeString() : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $logs->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection