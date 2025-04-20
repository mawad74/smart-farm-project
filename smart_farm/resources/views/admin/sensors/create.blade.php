@extends('layouts.dashboard')

@section('title', 'Add New Sensor')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Add New Sensor</h5>
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
                        <form method="POST" action="{{ route('admin.sensors.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Sensor Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Sensor Type</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Sensor Type</option>
                                    <option value="temperature" {{ old('type') === 'temperature' ? 'selected' : '' }}>Temperature</option>
                                    <option value="humidity" {{ old('type') === 'humidity' ? 'selected' : '' }}>Humidity</option>
                                    <option value="soil_moisture" {{ old('type') === 'soil_moisture' ? 'selected' : '' }}>Soil Moisture</option>
                                    <option value="ph" {{ old('type') === 'ph' ? 'selected' : '' }}>pH</option>
                                    <option value="nutrient" {{ old('type') === 'nutrient' ? 'selected' : '' }}>Nutrient</option>
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
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="faulty" {{ old('status') === 'faulty' ? 'selected' : '' }}>Faulty</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location (Optional)</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="light_intensity" class="form-label">Light Intensity (Optional)</label>
                                <input type="number" step="0.1" class="form-control @error('light_intensity') is-invalid @enderror" id="light_intensity" name="light_intensity" value="{{ old('light_intensity') }}">
                                @error('light_intensity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Add Sensor</button>
                            <a href="{{ route('admin.sensors.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection