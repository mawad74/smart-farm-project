@extends('layouts.dashboard')

@section('title', 'Report Details')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Report Details</h5>
                        <div class="mb-3">
                            <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $report->type)) }}
                        </div>
                        <div class="mb-3">
                            <strong>Farm:</strong> {{ $report->farm ? $report->farm->name : 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <strong>User:</strong> {{ $report->user ? $report->user->name : 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <strong>Created At:</strong> {{ $report->created_at->format('d M, Y') }}
                        </div>
                        <div class="mb-3">
                            <strong>Data:</strong>
                            <ul>
                                @foreach ($report->reportDetails as $detail)
                                    <li><strong>{{ ucfirst(str_replace('_', ' ', $detail->category)) }}:</strong> {{ $detail->value }} @if($detail->description) - {{ $detail->description }} @endif</li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Back to Reports</a>
                        <a href="{{ route('admin.reports.export', $report->id) }}" class="btn btn-success">Export to PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection