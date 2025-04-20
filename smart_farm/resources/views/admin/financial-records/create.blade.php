@extends('layouts.dashboard')

@section('title', 'Add New Financial Record')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Add New Financial Record</h5>
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
                        <form method="POST" action="{{ route('admin.financial-records.store') }}">
                            @csrf
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
                                <label for="type" class="form-label">Type</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="resource_cost" {{ old('type') === 'resource_cost' ? 'selected' : '' }}>Resource Cost</option>
                                    <option value="labor_cost" {{ old('type') === 'labor_cost' ? 'selected' : '' }}>Labor Cost</option>
                                    <option value="revenue" {{ old('type') === 'revenue' ? 'selected' : '' }}>Revenue</option>
                                    <option value="profit_loss" {{ old('type') === 'profit_loss' ? 'selected' : '' }}>Profit/Loss</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="value" class="form-label">Value</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" required>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="timestamp" class="form-label">Timestamp</label>
                                <input type="datetime-local" class="form-control @error('timestamp') is-invalid @enderror" id="timestamp" name="timestamp" value="{{ old('timestamp') }}" required>
                                @error('timestamp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Add Record</button>
                            <a href="{{ route('admin.financial-records.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection