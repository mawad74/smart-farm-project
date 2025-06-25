@extends('layouts.farmer_manager_dashboard')
@section('title', 'Report Details')
@section('dashboard-content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow-sm bg-gradient-report-details">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-file-alt fa-lg text-info" style="margin-left:8px;"></i>
                            <h5 class="card-title mb-0" style="font-size:1.1rem;">Report Details</h5>
                        </div>
                        <div class="d-flex gap-3">
                            <a href="{{ route('farmer_manager.reports.export', $report->id) }}" class="btn btn-success"><i class="fa-solid fa-file-pdf me-1"></i> Export PDF</a>
                            <a href="{{ route('farmer_manager.reports.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="report-info-box">
                                <div class="info-label">Type</div>
                                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $report->type)) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="report-info-box">
                                <div class="info-label">Farm</div>
                                <div class="info-value">{{ $report->farm ? $report->farm->name : 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="report-info-box">
                                <div class="info-label">User</div>
                                <div class="info-value">{{ $report->user ? $report->user->name : 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="report-info-box">
                                <div class="info-label">Created</div>
                                <div class="info-value">{{ $report->created_at->format('d M, Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="report-details-section mb-3">
                        <h6 class="mb-3 text-info"><i class="fa-solid fa-list-ul me-1"></i> Report Data</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Category</th>
                                        <th>Value</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report->reportDetails as $detail)
                                        <tr>
                                            <td>{{ ucfirst(str_replace('_', ' ', $detail->category)) }}</td>
                                            <td>{{ $detail->value }}</td>
                                            <td>{{ $detail->description ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
.bg-gradient-report-details {
    background: linear-gradient(135deg, #e3f0ff 60%, #f8f9fa 100%);
}
.card-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: #333;
    display: flex;
    align-items: center;
    gap: 7px;
}
.report-info-box {
    background: #f8fafd;
    border-radius: 10px;
    box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
    padding: 12px 8px 8px 8px;
    text-align: center;
    margin-bottom: 0.5rem;
}
.info-label {
    font-size: 0.93rem;
    color: #888;
    font-weight: 500;
    margin-bottom: 2px;
}
.info-value {
    font-size: 1.01rem;
    font-weight: 700;
    color: #0d6efd;
}
.d-flex.gap-3 > a.btn {
    margin-left: 18px;
}
.report-details-section {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
    padding: 22px 18px 18px 18px;
}
.table-bordered th, .table-bordered td {
    border: 1px solid #e3f0ff !important;
}
.table-light th {
    background: #e3f0ff !important;
    color: #0d6efd;
    font-weight: 600;
}
</style>
@endsection