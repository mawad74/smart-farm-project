@extends('layouts.farmer_manager_dashboard')
@section('title', 'Add New Schedule')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Add New Schedule</h5>
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
                        <form method="POST" action="{{ route('farmer_manager.schedules.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="plant_id" class="form-label">Plant</label>
                                <select class="form-control @error('plant_id') is-invalid @enderror" id="plant_id" name="plant_id" required>
                                    <option value="">Select Plant</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}" {{ old('plant_id') == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                                    @endforeach
                                </select>
                                @error('plant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="actuator_id" class="form-label">Actuator</label>
                                <select class="form-control @error('actuator_id') is-invalid @enderror" id="actuator_id" name="actuator_id" required>
                                    <option value="">Select Actuator</option>
                                    @foreach ($actuators as $actuator)
                                        <option value="{{ $actuator->id }}" {{ old('actuator_id') == $actuator->id ? 'selected' : '' }}>{{ ucfirst($actuator->type) }}</option>
                                    @endforeach
                                </select>
                                @error('actuator_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="schedule_time" class="form-label">Schedule Time</label>
                                <input type="datetime-local" class="form-control @error('schedule_time') is-invalid @enderror" id="schedule_time" name="schedule_time" value="{{ old('schedule_time') }}" required>
                                @error('schedule_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="weather_forecast_integration" class="form-label">Weather Forecast Integration</label>
                                <select class="form-control @error('weather_forecast_integration') is-invalid @enderror" id="weather_forecast_integration" name="weather_forecast_integration">
                                    <option value="0" {{ old('weather_forecast_integration', 0) === 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('weather_forecast_integration', 0) === 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('weather_forecast_integration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="priority_zone" class="form-label">Priority Zone</label>
                                <input type="text" class="form-control @error('priority_zone') is-invalid @enderror" id="priority_zone" name="priority_zone" value="{{ old('priority_zone') }}">
                                @error('priority_zone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Add Schedule</button>
                            <a href="{{ route('farmer_manager.schedules.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection