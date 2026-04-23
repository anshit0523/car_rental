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
        background: #ff6a00;
        border-radius: 20px;
        padding: 50px 40px;
        box-shadow: 0 40px 100px rgba(58, 58, 58, 0.6);
        color: #fff;
    }

    .auth-logo {
        text-align: center;
        margin-top: -40px;
        margin-bottom: 10px;
    }

    .auth-logo img {
        height: 100px;
    }

    .auth-subtitle {
        text-align: center;
        font-size: 14px;
        color: #ffffff;
        margin-bottom: 25px;
    }

    .form-label {
        color: #ffffff;
        font-size: 14px;
    }

    .form-control {
        background-color: #ffffff;
        border: 1px solid #ffffff;
        color: #000000;
        border-radius: 10px;
        padding: 12px 15px;
        width: 100%;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: #ff6a00;
        box-shadow: none;
        color: #000000;
    }

    .is-invalid {
        border: 2px solid #dc3545 !important;
    }

    .btn-login {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
        transition: 0.3s;
        color: #ff6a00;
    }

    .btn-login:hover {
        background: #ff8c2a;
        color: #ffffff;
    }

    .auth-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
        color: #000000;
    }

    .auth-footer a {
        color: #ffffff;
        text-decoration: none;
        font-weight: 500;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    .password-field {
        position: relative;
    }

    .password-field .form-control {
        padding-right: 50px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #fc8734;
        cursor: pointer;
        padding: 0;
        z-index: 3;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: #ff8c2a;
    }

    .password-toggle:focus {
        outline: none;
    }
</style>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-logo">
            <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Logo">
        </div>

        <p class="auth-subtitle">
            Sign in to your Dumaguete EZE Car Rental account
        </p>

        @error('email')
            <div class="alert alert-danger mb-3">
                {{ $message }}
            </div>
        @enderror

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" 
                    required
                >
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        name="password" 
                        id="login_password"
                        class="form-control @error('email') is-invalid @enderror"
                        required
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        data-target="login_password"
                        aria-label="Toggle password visibility">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.password-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (!input) return;

                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            });
        });
    });
</script>

@endsection