@extends('layouts.farmer_manager_dashboard')
@section('title', 'Add New Plant')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Add New Plant</h5>
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
                        <form method="POST" action="{{ route('farmer_manager.plants.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="vegetable" {{ old('type') === 'vegetable' ? 'selected' : '' }}>Vegetable</option>
                                    <option value="fruit" {{ old('type') === 'fruit' ? 'selected' : '' }}>Fruit</option>
                                    <option value="grain" {{ old('type') === 'grain' ? 'selected' : '' }}>Grain</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="health_status" class="form-label">Health Status</label>
                                <select class="form-control @error('health_status') is-invalid @enderror" id="health_status" name="health_status" required>
                                    <option value="">Select Health Status</option>
                                    <option value="healthy" {{ old('health_status') === 'healthy' ? 'selected' : '' }}>Healthy</option>
                                    <option value="diseased" {{ old('health_status') === 'diseased' ? 'selected' : '' }}>Diseased</option>
                                    <option value="needs_attention" {{ old('health_status') === 'needs_attention' ? 'selected' : '' }}>Needs Attention</option>
                                </select>
                                @error('health_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="growth_rate" class="form-label">Growth Rate</label>
                                <input type="number" class="form-control @error('growth_rate') is-invalid @enderror" id="growth_rate" name="growth_rate" value="{{ old('growth_rate') }}" step="0.1">
                                @error('growth_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="yield_prediction" class="form-label">Yield Prediction</label>
                                <input type="number" class="form-control @error('yield_prediction') is-invalid @enderror" id="yield_prediction" name="yield_prediction" value="{{ old('yield_prediction') }}" step="0.1">
                                @error('yield_prediction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Add Plant</button>
                            <a href="{{ route('farmer_manager.plants.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection