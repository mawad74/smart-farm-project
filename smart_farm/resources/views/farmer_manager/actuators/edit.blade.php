@extends('layouts.farmer_manager_dashboard')
@section('title', 'Edit Actuator')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Edit Actuator</h5>
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
                        <form method="POST" action="{{ route('farmer_manager.actuators.update', $actuator->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="plant_id" class="form-label">Plant</label>
                                <select class="form-control @error('plant_id') is-invalid @enderror" id="plant_id" name="plant_id">
                                    <option value="">Select Plant</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}" {{ old('plant_id', $actuator->plant_id) == $plant->id ? 'selected' : '' }}>{{ $plant->name }}</option>
                                    @endforeach
                                </select>
                                @error('plant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="irrigation_pump" {{ old('type', $actuator->type) === 'irrigation_pump' ? 'selected' : '' }}>Irrigation Pump</option>
                                    <option value="ventilation" {{ old('type', $actuator->type) === 'ventilation' ? 'selected' : '' }}>Ventilation</option>
                                    <option value="lighting" {{ old('type', $actuator->type) === 'lighting' ? 'selected' : '' }}>Lighting</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $actuator->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $actuator->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="faulty" {{ old('status', $actuator->status) === 'faulty' ? 'selected' : '' }}>Faulty</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="action_type" class="form-label">Action Type</label>
                                <input type="text" class="form-control @error('action_type') is-invalid @enderror" id="action_type" name="action_type" value="{{ old('action_type', $actuator->action_type) }}">
                                @error('action_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="last_triggered_at" class="form-label">Last Triggered At</label>
                                <input type="datetime-local" class="form-control @error('last_triggered_at') is-invalid @enderror" id="last_triggered_at" name="last_triggered_at" value="{{ old('last_triggered_at', $actuator->last_triggered_at ? $actuator->last_triggered_at->format('Y-m-d\TH:i') : '') }}">
                                @error('last_triggered_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Actuator</button>
                            <a href="{{ route('farmer_manager.actuators.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection