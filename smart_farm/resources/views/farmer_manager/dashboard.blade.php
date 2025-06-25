@extends('layouts.farmer_manager_dashboard')
@section('title', 'Farmer Manager Dashboard')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="card-title">My Dashboard</h5>
                                <p class="text-success">Welcome, {{ Auth::user()->name }}!</p>
                            </div>
                        </div>
                        @if ($farm)
                            <p class="mb-4"><strong>Farm:</strong> {{ $farm->name }}</p>
                        @else
                            <p class="text-muted mb-4">No farm assigned. Please contact the admin.</p>
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
        .card {
            border-radius: 5px;
        }
        .shadow-sm {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection