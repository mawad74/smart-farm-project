@extends('layouts.dashboard')

@section('title', 'Edit Schedule')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Edit Schedule</h5>
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
                        <form method="POST" action="{{ route('admin.schedules.update', $schedule->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="farm_id" class="form-label">Farm</label>
                                <select class="form-control @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                    <option value="">Select Farm</option>
                                    @foreach ($farms as $farm)
                                        <option value="{{ $farm->id }}" {{ old('farm_id', $schedule->farm_id) == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                                    @endforeach
                                </select>
                                @error('farm_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="plant_id" class="form-label">Plant</label>
                                <select class="form-control @error('plant_id') is-invalid @enderror" id="plant_id" name="plant_id" required>
                                    <option value="">Select Plant</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}" {{ old('plant_id', $schedule->plant_id) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
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
                                        <option value="{{ $actuator->id }}" {{ old('actuator_id', $schedule->actuator_id) == $actuator->id ? 'selected' : '' }}>{{ ucfirst($actuator->type) }}</option>
                                    @endforeach
                                </select>
                                @error('actuator_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="schedule_time" class="form-label">Schedule Time</label>
                                <input type="datetime-local" class="form-control @error('schedule_time') is-invalid @enderror" id="schedule_time" name="schedule_time" value="{{ old('schedule_time', $schedule->schedule_time->format('Y-m-d\TH:i')) }}" required>
                                @error('schedule_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ old('status', $schedule->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ old('status', $schedule->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="weather_forecast_integration" class="form-label">Weather Forecast Integration</label>
                                <select class="form-control @error('weather_forecast_integration') is-invalid @enderror" id="weather_forecast_integration" name="weather_forecast_integration" required>
                                    <option value="">Select Option</option>
                                    <option value="1" {{ old('weather_forecast_integration', $schedule->weather_forecast_integration) == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('weather_forecast_integration', $schedule->weather_forecast_integration) == '0' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('weather_forecast_integration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="priority_zone" class="form-label">Priority Zone (Optional)</label>
                                <input type="number" class="form-control @error('priority_zone') is-invalid @enderror" id="priority_zone" name="priority_zone" value="{{ old('priority_zone', $schedule->priority_zone) }}">
                                @error('priority_zone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Schedule</button>
                            <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection