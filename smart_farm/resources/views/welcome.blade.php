@extends('layouts.auth')

@section('title', 'Welcome')

@section('content')
    <section class="vh-100 background-login">
        <div class="container h-100">
            <div class="row d-flex align-items-center justify-content-center h-100">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="welcome-content form-signup">
                        <h1 class="text-center mb-2">Welcome to Smart Farm 
                            <img src="{{ asset('img/0565efa05a6b7d16cb232d2d628c6e6c.png') }}" 
                                 alt="Logo" style="width: 60px; height: auto; vertical-align: middle;">
                        </h1>
                        <p class="text-center mb-4">Manage your farm with ease and efficiency.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('login') }}" class="btn btn-success btn-lg mx-2">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Register</a>
                        </div>
                    </div>
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
        }

        .container {
            max-width: 1200px;
            padding: 0 15px;
        }

        /* Welcome content */
        .welcome-content {
            background-color: rgb(236, 236, 203);
            padding: 40px;
            border-radius: 30px;
            box-shadow: 15px 2px 4px rgba(0, 0, 0, 0.1), 3px 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
        }

        .welcome-content h1 {
            color: green;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 2rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-content h1 img {
            width: 60px;
            height: auto;
            margin-left: 10px;
            vertical-align: middle;
        }

        .welcome-content p {
            color: #555;
            font-size: 1rem;
        }

        .welcome-content .btn-success {
            background-color: rgb(85, 158, 11);
            border-radius: 10px;
            padding: 12px 24px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .welcome-content .btn-success:hover {
            background-color: rgb(65, 128, 0);
        }

        .welcome-content .btn-primary {
            background-color: #007bff;
            border-radius: 10px;
            padding: 12px 24px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .welcome-content .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .col-md-6, .col-lg-5 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .welcome-content {
                max-width: 100%;
                padding: 20px;
            }

            .welcome-content h1 {
                font-size: 1.5rem;
            }

            .welcome-content h1 img {
                width: 40px;
            }

            .welcome-content .btn {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
@endsection