@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <section class="vh-100 background-login">
        <div class="container-fluid py-5 h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <div class="col-12 col-md-7 col-lg-6 mb-4 mb-md-0">
                    <img src="{{ asset('img/eb8a03d20fc8ab0f6a2e940643c387fb.jpeg') }}"
                         class="img-fluid" alt="Login Image" style="max-width: 100%; height: 500px; object-fit: cover;">
                </div>
                <div class="col-12 col-md-5 col-lg-5">
                    <form method="POST" action="{{ route('login') }}" class="form-login">
                        @csrf
                        <div class="form-group d-flex flex-column align-items-center">
                            <h3 class="text-center mb-2">Login 
                                <img src="{{ asset('img/0565efa05a6b7d16cb232d2d628c6e6c (1).png') }}" 
                                     alt="Logo" style="width: 60px; height: auto; vertical-align: middle;">
                            </h3>
                            <p class="text-center mb-4">Welcome Back! Please login to your account</p>
                        </div>

                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                   placeholder="Email address">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" id="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password" 
                                   placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <!-- Checkbox -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" 
                                       id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-gray-600" for="remember">Remember me</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="btn-link">Forgot password?</a>
                            @endif
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-success btn-lg btn-block mb-3">Login</button>

                        <a href="{{ route('welcome') }}" class="index-link d-block mb-2 text-center">Go to Home Page</a>
                        <p class="text-center">Don’t Have An Account? 
                            <a href="{{ route('register') }}" class="btn-link">Sign Up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Background login */
        .background-login {
            background-color: rgb(238, 238, 199);
            margin: auto;
        }

        .container-fluid {
            max-width: 1200px;
            padding: 0 15px;
        }

        .img-fluid {
            border-radius: 160px 10px 160px 10px;
            max-width: 100%;
            height: 500px;
            object-fit: cover;
        }

        /* Form login */
        .form-login {
            background-color: rgb(236, 236, 203);
            padding: 40px;
            border-radius: 30px;
            box-shadow: 15px 2px 4px rgba(0, 0, 0, 0.1), 3px 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .form-login h3 {
            color: green;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 2rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-login h3 img {
            width: 60px;
            height: auto;
            margin-left: 10px;
        }

        .form-login p {
            color: #555;
            font-size: 1rem;
        }

        .form-login .form-control {
            background: transparent;
            border: 1px solid #000000;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-login .form-control::placeholder {
            color: rgb(85, 158, 11);
            font-size: 16px;
        }

        .form-login .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .form-login .btn-success {
            background-color: rgb(85, 158, 11);
            border-radius: 10px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .form-login .btn-success:hover {
            background-color: rgb(65, 128, 0);
        }

        .text-center {
            text-align: center;
        }

        .btn-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .btn-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .index-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .index-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .col-md-7, .col-md-5, .col-lg-6, .col-lg-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .img-fluid {
                margin-bottom: 20px;
                max-width: 80%;
                margin-left: auto;
                margin-right: auto;
                display: block;
                height: 300px;
            }

            .form-login {
                max-width: 100%;
                padding: 20px;
            }

            .form-login h3 {
                font-size: 1.5rem;
            }

            .form-login h3 img {
                width: 40px;
            }
        }
    </style>
@endsection