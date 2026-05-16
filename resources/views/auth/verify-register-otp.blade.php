@extends('layouts.auth')

@section('title', 'Verify OTP')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <h2 class="text-center mb-3">Verify Your Email</h2>

        <p class="text-center mb-4">
            We sent a 6-digit OTP to your email. Enter it below to create your account.
        </p>

        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.otp.verify') }}">
            @csrf

            <div class="mb-3">
                <label for="otp" class="form-label">OTP Code</label>
                <input
                    type="text"
                    name="otp"
                    id="otp"
                    maxlength="6"
                    inputmode="numeric"
                    class="form-control @error('otp') is-invalid @enderror"
                    placeholder="Enter 6-digit OTP"
                    value="{{ old('otp') }}"
                    required
                    autofocus
                >

                @error('otp')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Verify & Create Account
            </button>
        </form>

        <form method="POST" action="{{ route('register.otp.resend') }}" class="mt-3 text-center">
            @csrf
            <button type="submit" class="btn btn-link">
                Resend OTP
            </button>
        </form>
    </div>
</div>
@endsection