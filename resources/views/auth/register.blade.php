@extends('layouts.auth')
@section('title', 'Register')

@section('content')
  <h4 class="text-center mb-4">Create Your Account</h4>
  <form method="POST" action="{{ url('/register') }}">
    @csrf
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Confirm Password</label>
      <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100 mb-3">Register</button>
    <div class="text-center">
      <small>Already registered? <a href="{{ route('login') }}">Login</a></small>
    </div>
  </form>
@endsection
