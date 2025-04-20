@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <section class="vh-100 background-login">
        <div class="container h-100">
            <div class="row d-flex align-items-center justify-content-around h-100">
                <div class="col-12 col-md-5 col-lg-5 order-md-1 order-2">
                    <form method="POST" action="{{ route('password.email') }}" class="form-signup">
                        @csrf
                        <div class="form-group d-flex flex-column align-items-center">
                            <h3 class="text-center mb-2">Forgot Password 
                                <img src="{{ asset('img/0565efa05a6b7d16cb232d2d628c6e6c.png') }}" 
                                     alt="Logo" style="width: 60px; height: auto; vertical-align: middle;">
                            </h3>
                            <p class="text-center mb-4">Enter your email to receive a password reset link.</p>
                        </div>

                        <!-- Success Message -->
                        @if (session('status'))
                            <div class="alert alert-success text-center mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <!-- Email input -->
                        <div class="form-outline mb-4 position-relative">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email" 
                                   autofocus placeholder="| Email address">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-success btn-lg btn-block mb-3">Send Password Reset Link</button>

                        <a href="{{ route('welcome') }}" class="index-link d-block mb-2 text-center">Go to Home Page</a>
                        <p class="text-center">Back to 
                            <a href="{{ route('login') }}" class="btn-link">Login</a>
                        </p>
                    </form>
                </div>
                <div class="col-12 col-md-7 col-lg-6 mb-4 mb-md-0 order-md-2 order-1">
                    <img src="{{ asset('img/2.jpg') }}" 
                         class="img-fluid" alt="Forgot Password Image" 
                         style="border-radius: 160px 10px 160px 10px; width: 100%; height: 500px; object-fit: cover;">
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

        .img-fluid {
            border-radius: 160px 10px 160px 10px;
            width: 100%;
            height: 500px;
            object-fit: cover;
        }

        /* Form signup */
        .form-signup {
            background-color: rgb(236, 236, 203);
            padding: 40px;
            border-radius: 30px;
            box-shadow: 15px 2px 4px rgba(0, 0, 0, 0.1), 3px 8px 16px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
        }

        .form-signup h3 {
            color: green;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 2rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-signup h3 img {
            width: 60px;
            height: auto;
            margin-left: 10px;
            vertical-align: middle;
        }

        .form-signup p {
            color: #555;
            font-size: 1rem;
        }

        .form-signup .form-control {
            background: transparent;
            border: 1px solid #000000;
            border-radius: 5px;
            padding: 10px 10px 10px 40px;
            font-size: 16px;
            color: #333;
            position: relative;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-signup .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .form-signup .form-control::placeholder {
            color: rgb(85, 158, 11);
            font-size: 16px;
        }

        /* Icon inside Email Input */
        .form-outline input#email {
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='%23000000' d='M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.213.371-56.573-31.456-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.054 16.61 60.221 53.312 86.442 52.956 26.222.344 65.423-36.328 86.442-52.956 49.516-38.784 82.012-64.395 104.938-82.646V400H48z'/%3E%3C/svg%3E") no-repeat left 10px center;
            background-size: 20px 20px;
        }

        .form-signup .btn-success {
            background-color: rgb(85, 158, 11);
            border-radius: 10px;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .form-signup .btn-success:hover {
            background-color: rgb(65, 128, 0);
        }

        .form-outline {
            margin-bottom: 15px;
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
            margin-top: 10px;
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
                height: 300px;
                margin-left: auto;
                margin-right: auto;
                display: block;
            }

            .form-signup {
                max-width: 100%;
                padding: 20px;
            }

            .form-signup h3 {
                font-size: 1.5rem;
            }

            .form-signup h3 img {
                width: 40px;
            }
        }
    </style>
@endsection