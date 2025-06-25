@extends('layouts.farmer_manager_dashboard')
@section('title', 'Edit Control Command')
@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Edit Control Command</h5>
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
                        <form method="POST" action="{{ route('farmer_manager.control-commands.update', $command->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="actuator_id" class="form-label">Actuator</label>
                                <select class="form-control @error('actuator_id') is-invalid @enderror" id="actuator_id" name="actuator_id" required>
                                    <option value="">Select Actuator</option>
                                    @foreach ($actuators as $actuator)
                                        <option value="{{ $actuator->id }}" {{ old('actuator_id', $command->actuator_id) == $actuator->id ? 'selected' : '' }}>{{ ucfirst($actuator->type) }}</option>
                                    @endforeach
                                </select>
                                @error('actuator_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User</label>
                                <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $command->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="command_type" class="form-label">Command Type</label>
                                <select class="form-control @error('command_type') is-invalid @enderror" id="command_type" name="command_type" required>
                                    <option value="">Select Command Type</option>
                                    <option value="turn_on" {{ old('command_type', $command->command_type) === 'turn_on' ? 'selected' : '' }}>Turn On</option>
                                    <option value="turn_off" {{ old('command_type', $command->command_type) === 'turn_off' ? 'selected' : '' }}>Turn Off</option>
                                </select>
                                @error('command_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="executed_at" class="form-label">Executed At</label>
                                <input type="datetime-local" class="form-control @error('executed_at') is-invalid @enderror" id="executed_at" name="executed_at" value="{{ old('executed_at', $command->executed_at->format('Y-m-d\TH:i')) }}" required>
                                @error('executed_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="1" {{ old('status', $command->status) ? 'selected' : '' }}>Successful</option>
                                    <option value="0" {{ old('status', $command->status) == 0 ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Command</button>
                            <a href="{{ route('farmer_manager.control-commands.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection