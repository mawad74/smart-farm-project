@extends('layouts.farmer_manager_dashboard')
@section('title', 'Edit Profile')
@section('dashboard-content')
    <div class="container-fluid profile-edit-bg py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="profile-edit-card shadow-lg">
                    <div class="d-flex align-items-center mb-4">
                        <div class="profile-edit-icon me-3">
                            <i class="fa-solid fa-user-circle"></i>
                        </div>
                        <div>
                            <h2 class="profile-edit-title mb-1">Edit Profile</h2>
                            <div class="profile-edit-meta text-muted">Update your personal information</div>
                        </div>
                    </div>
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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('farmer_manager.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3 input-group-icon">
                            <span class="input-icon"><i class="fa-solid fa-user"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Full Name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 input-group-icon">
                            <span class="input-icon"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Email Address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 input-group-icon">
                            <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="New Password (Leave blank to keep current)">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 input-group-icon">
                            <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-success profile-edit-btn flex-fill"><i class="fa-solid fa-save me-1"></i> Update Profile</button>
                            <a href="{{ route('farmer_manager.dashboard') }}" class="btn btn-secondary flex-fill"><i class="fa-solid fa-arrow-left me-1"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
.profile-edit-bg {
    background: linear-gradient(120deg, #e3f0ff 60%, #f8f9fa 100%);
    min-height: 100vh;
}
.profile-edit-card {
    background: #fff;
    border-radius: 22px;
    box-shadow: 0 4px 32px 0 rgba(34,139,34,0.10);
    padding: 38px 32px 32px 32px;
    margin-top: 40px;
}
.profile-edit-icon {
    font-size: 3.2rem;
    color: #228B22;
    background: #e3f0ff;
    border-radius: 50%;
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px 0 rgba(34,139,34,0.08);
}
.profile-edit-title {
    font-size: 1.7rem;
    font-weight: 800;
    color: #228B22;
    letter-spacing: 1px;
}
.profile-edit-meta {
    font-size: 1.05rem;
    color: #888;
    margin-top: 2px;
}
.input-group-icon {
    position: relative;
}
.input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #228B22;
    opacity: 0.7;
    font-size: 1.1rem;
    z-index: 2;
}
.input-group-icon .form-control {
    padding-left: 38px;
    border-radius: 10px;
    box-shadow: 0 1px 4px 0 rgba(34,139,34,0.04);
    border: 1px solid #e3f0ff;
    transition: border 0.2s, box-shadow 0.2s;
}
.input-group-icon .form-control:focus {
    border: 1.5px solid #228B22;
    box-shadow: 0 2px 8px 0 rgba(34,139,34,0.10);
}
.profile-edit-btn {
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    padding: 10px 32px;
    background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
    border: none;
    color: #fff;
    box-shadow: 0 2px 8px 0 rgba(34,139,34,0.10);
    transition: background 0.2s, color 0.2s;
}
.profile-edit-btn:hover {
    background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
    color: #fff;
}
.btn-secondary {
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    padding: 10px 32px;
    background: #e3f0ff;
    color: #228B22;
    border: none;
    box-shadow: 0 2px 8px 0 rgba(34,139,34,0.06);
    transition: background 0.2s, color 0.2s;
}
.btn-secondary:hover {
    background: #228B22;
    color: #fff;
}
@media (max-width: 767px) {
    .profile-edit-card {
        padding: 18px 6px 16px 6px;
    }
}
</style>
@endsection