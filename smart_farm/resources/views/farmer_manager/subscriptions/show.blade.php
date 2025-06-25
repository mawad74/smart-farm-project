@extends('layouts.farmer_manager_dashboard')
@section('title', 'My Subscription')
@section('dashboard-content')
    <div class="container-fluid subscription-bg py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="subscription-card shadow-lg">
                    <div class="d-flex align-items-center mb-4">
                        <div class="subscription-icon me-3">
                            <i class="fa-solid fa-crown"></i>
                        </div>
                        <div>
                            <h2 class="subscription-title mb-1">Subscription Details</h2>
                            <div class="subscription-status">
                                <span class="badge status-badge {{ $subscription->status === 'active' ? 'bg-success' : ($subscription->status === 'expired' ? 'bg-danger' : 'bg-warning') }}">
                                    <i class="fa-solid {{ $subscription->status === 'active' ? 'fa-check-circle' : ($subscription->status === 'expired' ? 'fa-times-circle' : 'fa-exclamation-circle') }} me-1"></i>
                                    {{ ucfirst($subscription->status) }}
                            </span>
                            </div>
                        </div>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($isExpired)
                        <div class="alert alert-danger">
                            <strong>Warning:</strong> Your subscription has expired on {{ $subscription->end_date->format('d M, Y') }}.
                        </div>
                    @elseif ($isExpiringSoon)
                        <div class="alert alert-warning">
                            <strong>Alert:</strong> Your subscription will expire on {{ $subscription->end_date->format('d M, Y') }} (within 3 days).
                        </div>
                    @endif
                    <div class="row g-3 mb-2">
                        <div class="col-12 col-md-6">
                            <div class="subscription-info-box">
                                <i class="fa-solid fa-user subscription-info-icon"></i>
                                <span>{{ $subscription->user->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="subscription-info-box">
                                <i class="fa-solid fa-envelope subscription-info-icon"></i>
                                <span>{{ $subscription->user->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="subscription-info-box">
                                <i class="fa-solid fa-calendar-plus subscription-info-icon"></i>
                                <span>Start: {{ $subscription->start_date->format('d M, Y') ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="subscription-info-box">
                                <i class="fa-solid fa-calendar-minus subscription-info-icon"></i>
                                <span>End: {{ $subscription->end_date->format('d M, Y') ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="subscription-info-box">
                                <i class="fa-solid fa-clock subscription-info-icon"></i>
                                <span>Created: {{ $subscription->created_at->format('d M, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('farmer_manager.dashboard') }}" class="btn btn-secondary px-4"><i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.subscription-bg {
    background: linear-gradient(120deg, #e3f0ff 60%, #f8f9fa 100%);
    min-height: 100vh;
}
.subscription-card {
    background: #fff;
    border-radius: 22px;
    box-shadow: 0 4px 32px 0 rgba(34,139,34,0.10);
    padding: 38px 32px 32px 32px;
    margin-top: 40px;
}
.subscription-icon {
    font-size: 2.8rem;
    color: #f7b731;
    background: #fffbe6;
    border-radius: 50%;
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px 0 rgba(247,183,49,0.08);
}
.subscription-title {
    font-size: 1.7rem;
    font-weight: 800;
    color: #228B22;
    letter-spacing: 1px;
}
.status-badge {
    font-size: 1.05rem;
    padding: 7px 18px;
    border-radius: 18px;
    font-weight: 600;
    margin-top: 4px;
}
.subscription-info-box {
    background: #f8fafd;
    border-radius: 12px;
    box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.08rem;
    color: #333;
    margin-bottom: 8px;
}
.subscription-info-icon {
    font-size: 1.2rem;
    color: #228B22;
    opacity: 0.8;
}
.btn-secondary {
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    background: #e3f0ff;
    color: #228B22;
    border: none;
    transition: background 0.2s, color 0.2s;
}
.btn-secondary:hover {
    background: #228B22;
    color: #fff;
}
@media (max-width: 767px) {
    .subscription-card {
        padding: 18px 6px 16px 6px;
    }
}
</style>
@endsection