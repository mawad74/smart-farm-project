@extends('layouts.dashboard')

@section('title', 'Edit Alert')

@section('dashboard-content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Edit Alert</h5>
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
                        <form method="POST" action="{{ route('admin.alerts.update', $alert->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <input type="text" class="form-control" id="message" value="{{ $alert->message }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <input type="text" class="form-control" id="type" value="{{ ucfirst(str_replace('_', ' ', $alert->type)) }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="farm" class="form-label">Farm</label>
                                <input type="text" class="form-control" id="farm" value="{{ $alert->farm ? $alert->farm->name : 'N/A' }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="pending" {{ $alert->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="dismissed" {{ $alert->status === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                                    <option value="resolved" {{ $alert->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success">Update Alert</button>
                            <a href="{{ route('admin.alerts.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection