@extends('layouts.dashboard')

@section('title', 'Edit Weather Data')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Edit Weather Data</h5>
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
                        <form method="POST" action="{{ route('admin.weather-data.update', $weatherData->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="farm_id" class="form-label">Farm</label>
                                <select class="form-control @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                    <option value="">Select Farm</option>
                                    @foreach ($farms as $farm)
                                        <option value="{{ $farm->id }}" {{ old('farm_id', $weatherData->farm_id) == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                                    @endforeach
                                </select>
                                @error('farm_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="temperature" class="form-label">Temperature (°C)</label>
                                <input type="number" step="0.01" class="form-control @error('temperature') is-invalid @enderror" id="temperature" name="temperature" value="{{ old('temperature', $weatherData->temperature) }}" required>
                                @error('temperature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="rainfall" class="form-label">Rainfall (mm)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('rainfall') is-invalid @enderror" id="rainfall" name="rainfall" value="{{ old('rainfall', $weatherData->rainfall) }}" required>
                                @error('rainfall')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="wind_speed" class="form-label">Wind Speed (km/h)</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('wind_speed') is-invalid @enderror" id="wind_speed" name="wind_speed" value="{{ old('wind_speed', $weatherData->wind_speed) }}" required>
                                @error('wind_speed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="timestamp" class="form-label">Timestamp</label>
                                <input type="datetime-local" class="form-control @error('timestamp') is-invalid @enderror" id="timestamp" name="timestamp" value="{{ old('timestamp', $weatherData->timestamp->format('Y-m-d\TH:i')) }}" required>
                                @error('timestamp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Weather Data</button>
                            <a href="{{ route('admin.weather-data.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection