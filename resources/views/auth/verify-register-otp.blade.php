@extends('layouts.auth')

@section('title', 'Verify OTP')

@section('content')
<style>
    body {
        background: #f4f4f5;
    }

    .otp-page {
        min-height: 100vh;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .otp-shell {
        width: 100%;
        max-width: 1050px;
        min-height: 600px;
        background: #ffffff;
        border-radius: 22px;
        overflow: hidden;
        display: grid;
        grid-template-columns: 1.08fr 0.92fr;
        box-shadow: 0 22px 55px rgba(0, 0, 0, 0.12);
    }

    .otp-left {
        padding: 42px 48px;
        display: flex;
        align-items: center;
    }

    .otp-content {
        width: 100%;
    }

    .otp-icon {
        width: 62px;
        height: 62px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
    }

    .otp-icon i {
        font-size: 28px;
        color: #ff4d00;
    }

    .otp-title {
        font-size: 34px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 10px;
    }

    .otp-subtitle {
        font-size: 16px;
        line-height: 1.55;
        color: #64748b;
        margin-bottom: 24px;
        max-width: 500px;
    }

    .otp-alert {
        border-radius: 11px;
        padding: 13px 16px;
        margin-bottom: 12px;
        font-size: 14px;
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
        gap: 10px;
    }

    .otp-alert-icon {
        width: 24px;
        height: 24px;
        border-radius: 999px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
    }

    .otp-alert-success .otp-alert-icon {
        background: #22c55e;
    }

    .otp-alert-error .otp-alert-icon {
        background: #dc2626;
    }

    .otp-label {
        display: block;
        margin-top: 24px;
        margin-bottom: 12px;
        font-size: 17px;
        font-weight: 700;
        color: #111827;
    }

    .otp-boxes {
        display: flex;
        gap: 14px;
        margin-bottom: 24px;
    }

    .otp-box {
        width: 58px;
        height: 58px;
        border-radius: 10px;
        border: 1px solid #d8dee8;
        text-align: center;
        font-size: 26px;
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
        height: 54px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6a00, #f03300);
        color: white;
        font-size: 17px;
        font-weight: 700;
        box-shadow: 0 12px 22px rgba(255, 77, 0, 0.22);
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
        margin-top: 20px;
    }

    .resend-text {
        color: #64748b;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .resend-btn {
        border: none;
        background: transparent;
        color: #ff4d00;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .security-note {
        margin-top: 28px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
    }

    .security-note i {
        color: #94a3b8;
        font-size: 18px;
    }

    .otp-right {
        background: #050505;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .otp-side-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }

    @media (max-width: 1024px) {
        .otp-shell {
            max-width: 560px;
            min-height: auto;
            grid-template-columns: 1fr;
        }

        .otp-right {
            display: none;
        }

        .otp-left {
            padding: 36px 26px;
        }

        .otp-title {
            font-size: 30px;
        }

        .otp-subtitle {
            font-size: 15px;
        }

        .otp-boxes {
            gap: 9px;
            justify-content: space-between;
        }

        .otp-box {
            width: 48px;
            height: 54px;
            font-size: 23px;
        }
    }

    @media (max-width: 420px) {
        .otp-page {
            padding: 14px;
        }

        .otp-left {
            padding: 30px 18px;
        }

        .otp-box {
            width: 42px;
            height: 50px;
            font-size: 21px;
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

                    <button type="submit" class="resend-btn">
                        Resend OTP
                    </button>
                </form>

                <div class="security-note">
                    <i class="fas fa-shield-alt"></i>
                    <span>For your security, the OTP will expire in <strong>10 minutes.</strong></span>
                </div>
            </div>
        </div>

        <div class="otp-right">
           <img
    src="{{ Storage::disk('s3')->url('images/otp-side.png') }}"
    alt="Dumaguete EZE Car Rental OTP Side Design"
    class="otp-side-image"
>
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