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
    background: #ff6a00;
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
    color: #ffffff;
    margin-bottom: 28px;
  }

  .form-label {
    color: #ffffff;
    font-size: 14px;
    margin-bottom: 6px;
  }

  .form-control {
    background: #ffffff;
    border: 1px solid #ffffff;
    padding: 13px 15px;
    border-radius: 14px;
    color: #000000;
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.2);
    width: 100%;
  }

  .form-control::placeholder {
    color: rgba(0, 0, 0, 0.35);
  }

  .form-control:focus {
    background: #ffffff;
    border-color: #ff4d00;
    box-shadow: 0 0 0 .2rem rgba(255, 77, 0, .2);
    color: #000000;
  }

  .btn-auth {
    background: linear-gradient(90deg, #fc8734, #fc8734);
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
    background: linear-gradient(90deg, #ff8c2a, #ff8c2a);
    color: #ffffff;
  }

  .auth-footer {
    text-align: center;
    margin-top: 18px;
    color: #000000;
    font-size: 14px;
  }

  .auth-footer a {
    color: #ffffff;
    font-weight: 500;
    text-decoration: none;
  }

  .auth-footer a:hover {
    text-decoration: underline;
  }

  .input-error {
    color: #fff;
    font-size: 13px;
    margin-top: 6px;
    margin-bottom: 0;
  }

  .alert-error {
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.35);
    color: #fff;
    border-radius: 14px;
    padding: 12px 14px;
    margin-bottom: 18px;
    font-size: 14px;
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

  .phone-help {
    color: rgba(255, 255, 255, 0.88);
    font-size: 12px;
    margin-top: 6px;
    margin-bottom: 0;
  }
</style>

<div class="auth-wrapper">
  <div class="auth-card">

    <div class="auth-logo">
      <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Logo">
    </div>

    <h2 class="auth-title">Create Your Account</h2>
    <p class="auth-subtitle">Register to book faster and manage your rentals.</p>

    @if ($errors->any())
      <div class="alert-error">
        Please check the form and correct the highlighted fields.
      </div>
    @endif

    <form method="POST" action="{{ url('/register') }}" id="registerForm">
      @csrf

      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input
          type="text"
          name="name"
          class="form-control"
          placeholder="Enter your name"
          value="{{ old('name') }}"
          required
        >
        @error('name')
          <p class="input-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input
          type="email"
          name="email"
          class="form-control"
          placeholder="Enter your email"
          value="{{ old('email') }}"
          required
        >
        @error('email')
          <p class="input-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input
          type="tel"
          id="phone"
          name="phone"
          class="form-control"
          placeholder="09XX-XXX-XXXX"
          value="{{ old('phone') }}"
          maxlength="13"
          inputmode="numeric"
          required
        >
        <p class="phone-help">Format: 09XX-XXX-XXXX</p>
        <p id="phoneError" class="input-error" style="display:none;">
          Phone number must be 11 digits and start with 09.
        </p>
        @error('phone')
          <p class="input-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="password-field">
          <input
            type="password"
            name="password"
            id="register_password"
            class="form-control"
            placeholder="Create a password"
            required
          >
          <button
            type="button"
            class="password-toggle"
            data-target="register_password"
            aria-label="Toggle password visibility">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
        @error('password')
          <p class="input-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label">Confirm Password</label>
        <div class="password-field">
          <input
            type="password"
            name="password_confirmation"
            id="register_password_confirmation"
            class="form-control"
            placeholder="Confirm your password"
            required
          >
          <button
            type="button"
            class="password-toggle"
            data-target="register_password_confirmation"
            aria-label="Toggle password confirmation visibility">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
        @error('password_confirmation')
          <p class="input-error">{{ $message }}</p>
        @enderror
      </div>

      <button class="btn btn-auth w-100" type="submit">Register</button>

      <div class="auth-footer">
        Already have an account?
        <a href="{{ route('login') }}">Login</a>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('registerForm');
    const phone = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');

    function formatPhone(value) {
      const numbersOnly = value.replace(/\D/g, '').slice(0, 11);

      if (numbersOnly.length <= 4) {
        return numbersOnly;
      }

      if (numbersOnly.length <= 7) {
        return numbersOnly.slice(0, 4) + '-' + numbersOnly.slice(4);
      }

      return numbersOnly.slice(0, 4) + '-' + numbersOnly.slice(4, 7) + '-' + numbersOnly.slice(7);
    }

    function getRawPhone() {
      return phone ? phone.value.replace(/\D/g, '') : '';
    }

    function validatePhone(showError = true) {
      if (!phone) return true;

      const rawPhone = getRawPhone();
      const isValid = /^09\d{9}$/.test(rawPhone);

      if (phoneError) {
        phoneError.style.display = !isValid && showError ? 'block' : 'none';
      }

      phone.setCustomValidity(
        isValid ? '' : 'Phone number must be 11 digits and start with 09.'
      );

      return isValid;
    }

    if (phone) {
      phone.value = formatPhone(phone.value);

      phone.addEventListener('input', function () {
        phone.value = formatPhone(phone.value);
        validatePhone(false);
      });

      phone.addEventListener('keypress', function (e) {
        if (!/[0-9]/.test(e.key)) {
          e.preventDefault();
        }
      });

      phone.addEventListener('paste', function (e) {
        e.preventDefault();

        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        phone.value = formatPhone(pastedText);

        validatePhone(true);
      });

      phone.addEventListener('blur', function () {
        phone.value = formatPhone(phone.value);
        validatePhone(true);
      });
    }

    if (registerForm) {
      registerForm.addEventListener('submit', function (e) {
        if (!validatePhone(true)) {
          e.preventDefault();
          phone.focus();
          return;
        }

        // Submit clean 11-digit number to Laravel.
        // Example visible: 0946-979-4208
        // Example submitted: 09469794208
        phone.value = getRawPhone();
      });
    }

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