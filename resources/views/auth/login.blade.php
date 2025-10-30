@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<h4 class="text-center mb-4">Welcome Back</h4>

<form method="POST" action="{{ url('/login') }}">
  @csrf
  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button class="btn btn-primary w-100 mb-3">Login</button>
  <div class="text-center">
    <small>Don’t have an account? <a href="{{ route('register') }}">Sign up</a></small>
  </div>
</form>

<!-- Error Modal -->
<div class="modal fade" id="loginErrorModal" tabindex="-1" aria-labelledby="loginErrorLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="loginErrorLabel">Login Failed</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        {{ session('loginError') }}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@if (session('loginError'))
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var myModal = new bootstrap.Modal(document.getElementById('loginErrorModal'));
    myModal.show();
  });
</script>
@endif
@endsection
