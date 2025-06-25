@extends('layouts.farmer_manager_dashboard')
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
                        <form method="POST" action="{{ route('farmer_manager.weather-data.update', $weatherData->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $weatherData->date) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="temperature" class="form-label">Temperature (°C)</label>
                                <input type="number" class="form-control @error('temperature') is-invalid @enderror" id="temperature" name="temperature" value="{{ old('temperature', $weatherData->temperature) }}" step="0.1" required>
                                @error('temperature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="rainfall" class="form-label">Rainfall (mm)</label>
                                <input type="number" class="form-control @error('rainfall') is-invalid @enderror" id="rainfall" name="rainfall" value="{{ old('rainfall', $weatherData->rainfall) }}" step="0.1" required>
                                @error('rainfall')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="wind_speed" class="form-label">Wind Speed (km/h)</label>
                                <input type="number" class="form-control @error('wind_speed') is-invalid @enderror" id="wind_speed" name="wind_speed" value="{{ old('wind_speed', $weatherData->wind_speed) }}" step="0.1">
                                @error('wind_speed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Weather Data</button>
                            <a href="{{ route('farmer_manager.weather-data.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection