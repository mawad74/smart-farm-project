@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Reports')
@php
// Helper for random progress value (once per report)
@endphp
@section('dashboard-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm bg-gradient-report">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <h5 class="card-title"><i class="fas fa-chart-line text-primary me-2"></i> My Reports</h5>
                            <p class="text-success">Reports for <span class="fw-bold">{{ $farm->name }}</span></p>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#requestReportModal">
                                <i class="fa-solid fa-plus me-1"></i> Request New Report
                            </button>
                        </div>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            <i class="fa-solid fa-circle-xmark me-1"></i> {{ session('error') }}
                        </div>
                    @endif
                    @if (isset($pendingRequests) && (is_array($pendingRequests) || (is_object($pendingRequests) && method_exists($pendingRequests, 'isNotEmpty'))) && (is_array($pendingRequests) ? count($pendingRequests) : $pendingRequests->isNotEmpty()))
                        <div class="alert alert-warning mb-4">
                            <strong><i class="fa-solid fa-hourglass-half me-1"></i> Pending Requests:</strong>
                            <ul class="mb-0">
                                @foreach ($pendingRequests as $request)
                                    <li>Request for <b>{{ ucfirst(str_replace('_', ' ', $request->type)) }}</b> on <b>{{ $request->farm->name }}</b> - <span class="badge bg-warning text-dark">Status: {{ $request->status }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($reports->isEmpty())
                        <div class="empty-state-report">
                            <i class="fa-solid fa-file-circle-xmark fa-3x text-muted mb-2"></i>
                            <p class="text-muted">No reports available for <b>{{ $farm->name }}</b>.</p>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach ($reports as $report)
                                @php $progress = rand(60,100); @endphp
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="report-card shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center" style="gap: 10px;">
                                                <i class="fa-solid fa-file-alt fa-lg text-info" style="margin-right: 6px;"></i>
                                                <span class="fw-bold report-type" style="margin-left: 6px;">{{ ucfirst(str_replace('_', ' ', $report->type)) }}</span>
                                            </div>
                                            <div class="d-flex" style="gap: 12px;">
                                                <a href="{{ route('farmer_manager.reports.show', $report->id) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fa-solid fa-eye"></i></a>
                                                <a href="{{ route('farmer_manager.reports.export', $report->id) }}" class="btn btn-sm btn-outline-success" title="Export to PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 8px;">
                                            <span class="badge report-badge">User: {{ $report->user ? $report->user->name : 'N/A' }}</span>
                                            <span class="badge report-badge">Date: {{ $report->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="progress report-progress mb-2" style="height: 7px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="report-summary text-muted">
                                            <i class="fa-solid fa-circle-info me-1"></i>
                                            <span>Click <b>View</b> to see full report details and analytics.</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <p class="text-muted">Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} data</p>
                            {{ $reports->appends(request()->query())->links() }}
                        </div>
                    @endif
                    <!-- Modal for Report Request -->
                    <div class="modal fade" id="requestReportModal" tabindex="-1" aria-labelledby="requestReportModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="requestReportModalLabel">Request New Report</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('farmer_manager.reports.storeRequest') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type</label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="system_performance">System Performance</option>
                                                <option value="alert_history">Alert History</option>
                                                <option value="environmental_conditions">Environmental Conditions</option>
                                                <option value="resource_usage">Resource Usage</option>
                                                <option value="crop_health">Crop Health</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="farm_id" class="form-label">Farm</label>
                                            <select class="form-control @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                                <option value="">Select Farm</option>
                                                @foreach (Auth::user()->farms as $farm)
                                                    <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('farm_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-success">Submit Request</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.bg-gradient-report {
    background: linear-gradient(135deg, #e3f0ff 60%, #f8f9fa 100%);
}
.report-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 6px 18px 0 rgba(0,0,0,0.10);
    margin-bottom: 14px;
    transition: box-shadow 0.2s;
    border: 1.5px solid #e3f0ff;
    padding: 22px 18px 18px 18px;
    min-height: 170px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    position: relative;
}
.report-card:hover {
    box-shadow: 0 12px 32px 0 rgba(0,0,0,0.16);
    transform: translateY(-2px) scale(1.01);
}
.report-type {
    color: #0d6efd;
    font-size: 1.08rem;
    margin-left: 6px;
    margin-right: 0;
}
.report-badge {
    background: #e3f0ff;
    border: 1px solid #b6d4fe;
    color: #0d6efd;
    font-size: 0.89rem;
    border-radius: 8px;
    margin-bottom: 1px;
    font-weight: 500;
    padding: 5px 10px;
}
.report-progress {
    background: #e3f0ff;
    border-radius: 6px;
}
.report-summary {
    font-size: 0.97rem;
    color: #6c757d;
    margin-top: 2px;
}
.empty-state-report {
    text-align: center;
    padding: 40px 0 30px 0;
}
.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeeba;
    color: #856404;
}
</style>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('requestReportForm');
            if (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfTokenMeta) {
                        console.error('CSRF token meta tag not found');
                        alert('An error occurred: CSRF token is missing.');
                        return;
                    }

                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.success);
                            $('#requestReportModal').modal('hide');
                            location.reload();
                        } else if (data.error) {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error during submission:', error);
                        alert('An error occurred while submitting the request. Check the console for details. Error: ' + error.message);
                    });
                });
            } else {
                console.error('Request form not found');
            }
        });
    </script>
@endsection