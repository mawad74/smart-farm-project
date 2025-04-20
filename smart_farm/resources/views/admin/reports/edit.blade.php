@extends('layouts.dashboard')

@section('title', 'Edit Report')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Edit Report</h5>
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
                        <form method="POST" action="{{ route('admin.reports.update', $report->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="system_performance" {{ old('type', $report->type) === 'system_performance' ? 'selected' : '' }}>System Performance</option>
                                    <option value="alert_history" {{ old('type', $report->type) === 'alert_history' ? 'selected' : '' }}>Alert History</option>
                                    <option value="environmental_conditions" {{ old('type', $report->type) === 'environmental_conditions' ? 'selected' : '' }}>Environmental Conditions</option>
                                    <option value="resource_usage" {{ old('type', $report->type) === 'resource_usage' ? 'selected' : '' }}>Resource Usage</option>
                                    <option value="crop_health" {{ old('type', $report->type) === 'crop_health' ? 'selected' : '' }}>Crop Health</option>
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
                                        <option value="{{ $farm->id }}" {{ old('farm_id', $report->farm_id) == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
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
                                        <option value="{{ $user->id }}" {{ old('user_id', $report->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Report</button>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection