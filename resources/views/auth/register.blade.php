@extends('layouts.auth')
@section('title', 'Register')

@section('content')

  <style>
    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 12px;

    }

    .auth-card {
      width: 100%;
      max-width: 450px;
      background: #1b1b1b;
      border-radius: 26px;
      padding: 90px 45px 40px;
      position: relative;
      color: #fff;
      box-shadow: 0 40px 120px rgba(0, 0, 0, 0.7);
      border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .auth-logo {
      text-align: center;
      margin-top: -70px;
      margin-bottom: 1px;
    }

    .auth-logo img {
      height: 90px;
      width: 40%;

    }

    .auth-title {
      text-align: center;
      font-weight: 700;
      font-size: 26px;
      margin-bottom: 8px;
    }

    .auth-subtitle {
      text-align: center;
      font-size: 14px;
      color: #bdbdbd;
      margin-bottom: 28px;
    }

    .form-label {
      color: #e6e6e6;
      font-size: 14px;
      margin-bottom: 6px;
    }

    .form-control {
      background: #111;
      border: 1px solid #333;
      padding: 13px 15px;
      border-radius: 14px;
      color: #fff;
      box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.2);
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.35);
    }

    .form-control:focus {
      background: #111;
      border-color: #ff4d00;
      box-shadow: 0 0 0 .2rem rgba(255, 77, 0, .2);
      color: #fff;
    }

    .btn-auth {
      background: linear-gradient(90deg, #ff4d00, #ff6a00);
      border: none;
      border-radius: 12px;
      padding: 12px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: 0.3s;
      color: #fff;

    }

    .btn-auth:hover {
      transform: translateY(-1px);
      opacity: .97;
    }

    .auth-footer {
      text-align: center;
      margin-top: 18px;
      color: #a8a8a8;
      font-size: 14px;
    }

    .auth-footer a {
      color: #ff6a00;
      font-weight: 500;
      text-decoration: none;
    }

    .auth-footer a:hover {
      text-decoration: underline;
    }
  </style>

  <div class="auth-wrapper">
    <div class="auth-card">

      <!-- LOGO -->
      <div class="auth-logo">
        <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Logo">
      </div>

      <h2 class="auth-title">Create Your Account</h2>
      <p class="auth-subtitle">Register to book faster and manage your rentals.</p>

      <form method="POST" action="{{ url('/register') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}"
            required>
        </div>

        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" placeholder="Enter your phone number"
            value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Create a password"
            value="{{ old('password') }}" required>
        </div>

        <div class="mb-4">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm your password"
            value="{{ old('password_confirmation') }}" required>
        </div>

        <button class="btn btn-auth w-100">Register</button>

        <div class="auth-footer">
          Already have an account?
          <a href="{{ route('login') }}">Login</a>
        </div>

      </form>
    </div>
  </div>

@endsection