@extends('layouts.auth')

@section('title', 'Verify OTP')

@section('content')
<style>
    body {
        background: #f5f5f5;
    }

    .otp-page {
        min-height: 100vh;
        padding: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .otp-shell {
        width: 100%;
        max-width: 1280px;
        min-height: 720px;
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1fr;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.12);
    }

    .otp-left {
        padding: 55px 60px;
        display: flex;
        align-items: center;
    }

    .otp-content {
        width: 100%;
    }

    .otp-icon {
        width: 78px;
        height: 78px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 34px;
    }

    .otp-icon i {
        font-size: 34px;
        color: #ff4d00;
    }

    .otp-title {
        font-size: 42px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 14px;
    }

    .otp-subtitle {
        font-size: 19px;
        line-height: 1.6;
        color: #64748b;
        margin-bottom: 32px;
        max-width: 560px;
    }

    .otp-alert {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 16px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .otp-alert-success {
        background: #ecfdf3;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .otp-alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }

    .otp-alert-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .otp-alert-icon {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .otp-alert-success .otp-alert-icon {
        background: #22c55e;
    }

    .otp-alert-error .otp-alert-icon {
        background: #dc2626;
    }

    .otp-label {
        display: block;
        margin-top: 30px;
        margin-bottom: 14px;
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    .otp-boxes {
        display: flex;
        gap: 18px;
        margin-bottom: 28px;
    }

    .otp-box {
        width: 76px;
        height: 76px;
        border-radius: 10px;
        border: 1px solid #d8dee8;
        text-align: center;
        font-size: 32px;
        font-weight: 700;
        color: #111827;
        outline: none;
        transition: 0.2s ease;
    }

    .otp-box:focus {
        border-color: #ff4d00;
        box-shadow: 0 0 0 4px rgba(255, 77, 0, 0.12);
    }

    .otp-submit {
        width: 100%;
        height: 64px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6a00, #f03300);
        color: white;
        font-size: 20px;
        font-weight: 700;
        box-shadow: 0 14px 25px rgba(255, 77, 0, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: 0.2s ease;
    }

    .otp-submit:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #ff7a1a, #ff3d00);
    }

    .resend-wrap {
        text-align: center;
        margin-top: 26px;
    }

    .resend-text {
        color: #64748b;
        font-size: 17px;
        margin-bottom: 6px;
    }

    .resend-btn {
        border: none;
        background: transparent;
        color: #ff4d00;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
    }

    .security-note {
        margin-top: 38px;
        padding: 18px 22px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 16px;
    }

    .security-note i {
        color: #94a3b8;
        font-size: 22px;
    }

    .otp-right {
        position: relative;
        background:
            linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.6)),
            url("{{ asset('images/otp-vios-bg.png') }}");
        background-size: cover;
        background-position: center;
        color: #fff;
        padding: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .otp-right-content {
        width: 100%;
        text-align: center;
    }

    .brand-logo {
        width: 360px;
        max-width: 80%;
        margin: 0 auto 30px;
        display: block;
    }

    .brand-title {
        font-size: 34px;
        font-weight: 900;
        line-height: 1.25;
        text-transform: uppercase;
        margin-bottom: 28px;
    }

    .brand-title span {
        color: #ff4d00;
    }

    .brand-features {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 260px;
    }

    .feature-item {
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .feature-icon {
        width: 62px;
        height: 62px;
        border-radius: 999px;
        border: 2px solid #ff4d00;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 24px;
        background: rgba(0, 0, 0, 0.35);
    }

    .steps {
        position: absolute;
        bottom: 25px;
        left: 60px;
        right: 60px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        text-align: center;
    }

    .step-circle {
        width: 64px;
        height: 64px;
        border-radius: 999px;
        border: 2px solid #ff4d00;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 24px;
        background: rgba(0, 0, 0, 0.6);
    }

    .step-number {
        color: #ff4d00;
        font-size: 28px;
        font-weight: 900;
    }

    .step-label {
        font-size: 16px;
        font-weight: 600;
    }

    @media (max-width: 1024px) {
        .otp-shell {
            grid-template-columns: 1fr;
        }

        .otp-right {
            display: none;
        }

        .otp-left {
            padding: 40px 24px;
        }

        .otp-title {
            font-size: 34px;
        }

        .otp-boxes {
            gap: 10px;
        }

        .otp-box {
            width: 52px;
            height: 60px;
            font-size: 26px;
        }
    }
</style>

<div class="otp-page">
    <div class="otp-shell">

        <div class="otp-left">
            <div class="otp-content">
                <div class="otp-icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>

                <h1 class="otp-title">Verify Your Email</h1>

                <p class="otp-subtitle">
                    We sent a 6-digit OTP to your email. Enter the code below to complete your account registration.
                </p>

                @if(session('success'))
                    <div class="otp-alert otp-alert-success">
                        <div class="otp-alert-left">
                            <span class="otp-alert-icon">✓</span>
                            <span>{{ session('success') }}</span>
                        </div>
                        <span>×</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="otp-alert otp-alert-error">
                        <div class="otp-alert-left">
                            <span class="otp-alert-icon">!</span>
                            <span>{{ session('error') }}</span>
                        </div>
                        <span>×</span>
                    </div>
                @endif

                @error('otp')
                    <div class="otp-alert otp-alert-error">
                        <div class="otp-alert-left">
                            <span class="otp-alert-icon">!</span>
                            <span>{{ $message }}</span>
                        </div>
                        <span>×</span>
                    </div>
                @enderror

                <form method="POST" action="{{ route('register.otp.verify') }}" id="otpForm">
                    @csrf

                    <input type="hidden" name="otp" id="otp" value="{{ old('otp') }}">

                    <label class="otp-label">OTP Code</label>

                    <div class="otp-boxes">
                        @for ($i = 0; $i < 6; $i++)
                            <input
                                type="text"
                                maxlength="1"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                class="otp-box"
                                autocomplete="off"
                            >
                        @endfor
                    </div>

                    <button type="submit" class="otp-submit">
                        Verify & Create Account
                        <span>→</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('register.otp.resend') }}" class="resend-wrap">
                    @csrf
                    <p class="resend-text">Didn't receive the code?</p>
                    <button type="submit" class="resend-btn">Resend OTP</button>
                </form>

                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>For your security, the OTP will expire in <strong>10 minutes.</strong></span>
                </div>
            </div>
        </div>

        <div class="otp-right">
            <div class="otp-right-content">
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Dumaguete EZE Car Rental"
                    class="brand-logo"
                >

                <div class="brand-title">
                    Drive Dumaguete<br>
                    with <span>Comfort</span> & <span>Confidence</span>
                </div>

                <div class="brand-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        Reliable<br>Vehicles
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        Trusted<br>Service
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-thumbs-up"></i>
                        </div>
                        Your Journey,<br>Our Priority
                    </div>
                </div>

                <div class="steps">
                    <div>
                        <div class="step-circle">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="step-number">1</div>
                        <div class="step-label">Register</div>
                    </div>

                    <div>
                        <div class="step-circle">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="step-number">2</div>
                        <div class="step-label">Verify</div>
                    </div>

                    <div>
                        <div class="step-circle">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="step-number">3</div>
                        <div class="step-label">Book</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const boxes = document.querySelectorAll('.otp-box');
        const hiddenOtp = document.getElementById('otp');
        const form = document.getElementById('otpForm');

        function updateOtpValue() {
            hiddenOtp.value = Array.from(boxes).map(box => box.value).join('');
        }

        if (hiddenOtp.value) {
            hiddenOtp.value.split('').forEach((digit, index) => {
                if (boxes[index]) {
                    boxes[index].value = digit;
                }
            });
        }

        boxes.forEach((box, index) => {
            box.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');

                if (this.value && index < boxes.length - 1) {
                    boxes[index + 1].focus();
                }

                updateOtpValue();
            });

            box.addEventListener('keydown', function (event) {
                if (event.key === 'Backspace' && !this.value && index > 0) {
                    boxes[index - 1].focus();
                }
            });

            box.addEventListener('paste', function (event) {
                event.preventDefault();

                const pasted = (event.clipboardData || window.clipboardData)
                    .getData('text')
                    .replace(/[^0-9]/g, '')
                    .slice(0, 6);

                pasted.split('').forEach((digit, pasteIndex) => {
                    if (boxes[pasteIndex]) {
                        boxes[pasteIndex].value = digit;
                    }
                });

                updateOtpValue();

                if (boxes[pasted.length - 1]) {
                    boxes[pasted.length - 1].focus();
                }
            });
        });

        form.addEventListener('submit', function () {
            updateOtpValue();
        });
    });
</script>
@endsection