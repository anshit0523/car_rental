@extends('layouts.auth')
@section('title', 'Login')

@section('content')

    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            background: rgba(28, 28, 28, 0.98);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
            color: #fff;
        }

        .auth-logo {
            text-align: center;
            margin-top: -40px;
            margin-bottom: 1px;
        }

        .auth-logo img {
            height: 100px;

        }

        .auth-title {
            text-align: center;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .auth-subtitle {
            text-align: center;
            font-size: 14px;
            color: #aaa;
            margin-bottom: 30px;
        }

        .form-label {
            color: #ddd;
            font-size: 14px;
        }

        .form-control {
            background-color: #111;
            border: 1px solid #333;
            color: #fff;
            border-radius: 10px;
            padding: 12px 15px;
        }

        .form-control:focus {
            background-color: #111;
            border-color: #ff4d00;
            box-shadow: none;
            color: #fff;
        }

        .btn-login {
            background: linear-gradient(90deg, #ff4d00, #ff6a00);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: 0.3s;
            color: #fff;
        }

        .btn-login:hover {
            opacity: 0.9;
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #aaa;
        }

        .auth-footer a {
            color: #ff4d00;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>

    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="auth-logo">

                <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Logo" style="height: 100px;">

            </div>

            <h4 class="auth-title">Welcome Back</h4>
            <p class="auth-subtitle">
                Sign in to your Dumaguete EZE Car Rental account
            </p>

            <form method="POST" action="{{ url('/login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn btn-login w-100">
                    Sign In
                </button>

                <div class="auth-footer">
                    Don’t have an account?
                    <a href="{{ route('register') }}">Create Account</a>
                </div>
            </form>

        </div>
    </div>

@endsection