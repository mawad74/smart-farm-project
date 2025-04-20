@extends('layouts.dashboard')

@section('title', 'Add New Report')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Add New Report</h5>
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
                        <form method="POST" action="{{ route('admin.reports.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="system_performance" {{ old('type') === 'system_performance' ? 'selected' : '' }}>System Performance</option>
                                    <option value="alert_history" {{ old('type') === 'alert_history' ? 'selected' : '' }}>Alert History</option>
                                    <option value="environmental_conditions" {{ old('type') === 'environmental_conditions' ? 'selected' : '' }}>Environmental Conditions</option>
                                    <option value="resource_usage" {{ old('type') === 'resource_usage' ? 'selected' : '' }}>Resource Usage</option>
                                    <option value="crop_health" {{ old('type') === 'crop_health' ? 'selected' : '' }}>Crop Health</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="farm_id" class="form-label">Farm</label>
                                <select class="form-control @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                    <option value="">Select Farm</option>
                                    @foreach ($farms as $farm)
                                        <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                                    @endforeach
                                </select>
                                @error('farm_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User</label>
                                <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Add Report</button>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection