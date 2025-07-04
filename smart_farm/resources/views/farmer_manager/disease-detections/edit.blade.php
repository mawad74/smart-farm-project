@extends('layouts.farmer_manager_dashboard')
@section('title', 'Edit Disease Detection')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Edit Disease Detection</h5>
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
                        <form method="POST" action="{{ route('farmer_manager.disease-detections.update', $detection->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="plant_id" class="form-label">Plant</label>
                                <select class="form-control @error('plant_id') is-invalid @enderror" id="plant_id" name="plant_id" required>
                                    <option value="">Select Plant</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}" {{ old('plant_id', $detection->plant_id) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                                    @endforeach
                                </select>
                                @error('plant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="disease_name" class="form-label">Disease Name</label>
                                <input type="text" class="form-control @error('disease_name') is-invalid @enderror" id="disease_name" name="disease_name" value="{{ old('disease_name', $detection->disease_name) }}" required>
                                @error('disease_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="confidence" class="form-label">Confidence</label>
                                <input type="number" class="form-control @error('confidence') is-invalid @enderror" id="confidence" name="confidence" value="{{ old('confidence', $detection->confidence) }}" step="0.01" min="0" max="1" required>
                                @error('confidence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Detection</button>
                            <a href="{{ route('farmer_manager.disease-detections.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection