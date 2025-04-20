@extends('layouts.dashboard')

@section('title', 'Sensor Readings')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">Readings for Sensor: {{ $sensor->name }}</h5>
                                <p class="text-success">Type: {{ ucfirst(str_replace('_', ' ', $sensor->type)) }}</p>
                            </div>
                            <div>
                                <a href="{{ route('admin.sensors.index') }}" class="btn btn-secondary">Back to Sensors</a>
                            </div>
                        </div>
                        @if ($readings->isEmpty())
                            <p class="text-muted">No readings available for this sensor.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Timestamp</th>
                                        <th>Value</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($readings as $reading)
                                        <tr>
                                            <td>{{ $reading->id }}</td>
                                            <td>{{ $reading->timestamp->format('d M, Y H:i:s') }}</td>
                                            <td>{{ $reading->value }}</td>
                                            <td>{{ $reading->created_at->format('d M, Y H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <p class="text-muted">Showing {{ $readings->firstItem() }} to {{ $readings->lastItem() }} of {{ $readings->total() }} data</p>
                                {{ $readings->links() }}
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

        .table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
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