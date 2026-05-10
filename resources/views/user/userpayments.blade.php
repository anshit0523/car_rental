@extends('layouts.userlayout')

@section('custom-styles')
<style>
    .payment-page {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(255, 90, 31, 0.07), transparent 34%),
            linear-gradient(135deg, #f8fafc 0%, #ffffff 45%, #fff7ed 100%);
        font-family: 'Outfit', sans-serif;
    }

    .payment-shell {
        max-width: 1450px;
        margin: 0 auto;
        padding: 30px 22px 54px;
    }

    .payment-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .payment-back {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #0f172a;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .payment-back:hover {
        transform: translateY(-1px);
        color: #ff5a1f;
        border-color: #fed7aa;
    }

    .payment-title {
        margin: 0;
        font-size: 34px;
        line-height: 1.05;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.03em;
    }

    .payment-header-subtitle {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }

    .payment-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(340px, 430px);
        gap: 26px;
        align-items: start;
    }

    .payment-card {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid #eef2f7;
        border-radius: 28px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        padding: 28px;
        backdrop-filter: blur(10px);
    }

    .payment-card-head {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 22px;
    }

    .payment-card-head-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        background: #fff7ed;
        color: #ff5a1f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        border: 1px solid #fed7aa;
    }

    .payment-card-title {
        margin: 0;
        font-size: 21px;
        line-height: 1.2;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .payment-card-subtitle {
        margin: 5px 0 0;
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        font-weight: 500;
    }

    .payment-method-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .payment-option {
        min-height: 190px;
        border: 1.5px solid #e5e7eb;
        border-radius: 24px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: #ffffff;
        transition: all 0.25s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .payment-option::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 90, 31, 0.08), transparent 55%);
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .payment-option:hover {
        border-color: #fed7aa;
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
    }

    .payment-option:hover::before {
        opacity: 1;
    }

    .payment-option.is-active {
        border-color: #ff5a1f;
        background: #fff7ed;
        box-shadow: 0 16px 35px rgba(255, 90, 31, 0.13);
    }

    .payment-option.is-active::before {
        opacity: 1;
    }

    .payment-radio-dot {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        border: 2px solid #cbd5e1;
        background: #ffffff;
        position: absolute;
        top: 18px;
        right: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .payment-option.is-active .payment-radio-dot {
        background: #ff5a1f;
        border-color: #ff5a1f;
        color: #ffffff;
    }

    .payment-option.is-active .payment-radio-dot::after {
        content: "✓";
        font-size: 13px;
        font-weight: 800;
    }

    .payment-option-icon {
        width: 82px;
        height: 82px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    .payment-option-icon.bank-icon {
        background: #fff7ed;
        color: #ea580c;
    }

    .payment-option-icon.gcash-icon {
        background: #ffffff;
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.18);
        overflow: hidden;
    }

    .gcash-logo-img {
        width: 82px;
        height: 82px;
        object-fit: cover;
        border-radius: 999px;
        display: block;
    }

    .payment-option-label {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        position: relative;
        z-index: 1;
    }

    .payment-option-note {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
        position: relative;
        z-index: 1;
    }

    .payment-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .payment-panel {
        border: 1px solid #fed7aa;
        border-radius: 24px;
        padding: 24px;
        background: linear-gradient(135deg, #fffaf5, #ffffff);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .payment-panel.hidden-panel {
        display: none;
    }

    .payment-panel-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .payment-panel-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        background: #fff7ed;
        color: #ff5a1f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .payment-panel-title {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .payment-panel-subtitle {
        margin: 3px 0 0;
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
        line-height: 1.5;
    }

    .payment-split {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 270px;
        gap: 24px;
        align-items: stretch;
    }

    .provider-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .provider-badge {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #ffffff;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.16);
    }

    .provider-badge img {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border-radius: 14px;
        display: block;
    }

    .provider-name {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .divider-line {
        height: 1px;
        background: #e5e7eb;
        margin: 16px 0;
    }

    .upload-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .upload-subtitle {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 12px;
        line-height: 1.5;
        font-weight: 500;
    }

    .upload-box {
        border: 1.5px dashed #fdba74;
        border-radius: 20px;
        padding: 28px 18px;
        background: #ffffff;
        transition: all 0.25s ease;
        text-align: center;
    }

    .upload-box:hover {
        border-color: #ff5a1f;
        background: #fff7ed;
    }

    .upload-box label {
        display: block;
        cursor: pointer;
    }

    .upload-box-icon {
        width: 58px;
        height: 58px;
        border-radius: 999px;
        background: #fff7ed;
        color: #ff5a1f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 24px;
    }

    .upload-main {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .upload-help {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .preview-box {
        margin-top: 12px;
        display: none;
    }

    .preview-box img {
        width: 170px;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .payment-qr-wrap {
        text-align: center;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .payment-qr-wrap img {
        width: 190px;
        max-width: 100%;
        margin: 0 auto 12px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid #e5e7eb;
        padding: 8px;
        display: block;
    }

    .payment-qr-name {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .payment-qr-number {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }

    .bank-details {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .bank-row {
        display: grid;
        grid-template-columns: 42px 1fr auto;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border-bottom: 1px solid #eef2f7;
        font-size: 14px;
    }

    .bank-row:last-child {
        border-bottom: none;
    }

    .bank-row-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #fff7ed;
        color: #ff5a1f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .bank-row span:nth-child(2) {
        color: #64748b;
        font-weight: 500;
    }

    .bank-row span:last-child {
        color: #0f172a;
        font-weight: 700;
        text-align: right;
    }

    .submit-payment-btn {
        width: 100%;
        min-height: 54px;
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #ff5a1f, #f97316);
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        transition: all 0.25s ease;
        box-shadow: 0 14px 28px rgba(249, 115, 22, 0.22);
    }

    .submit-payment-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 34px rgba(249, 115, 22, 0.30);
    }

    .submit-payment-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .summary-card {
        position: sticky;
        top: 110px;
    }

    .summary-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .summary-head-icon {
        width: 44px;
        height: 44px;
        border-radius: 15px;
        background: #eff6ff;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .summary-title {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .summary-subtitle {
        margin: 3px 0 0;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .summary-car {
        display: flex;
        gap: 14px;
        margin-bottom: 18px;
        align-items: center;
    }

    .summary-car img {
        width: 94px;
        height: 82px;
        border-radius: 16px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid #e5e7eb;
    }

    .summary-car-name {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .summary-car-meta {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
        line-height: 1.5;
    }

    .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .summary-detail-list {
        border-top: 1px solid #e5e7eb;
        padding-top: 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 14px;
    }

    .summary-detail-item {
        display: grid;
        grid-template-columns: 34px 1fr auto;
        align-items: center;
        gap: 10px;
        font-size: 13px;
    }

    .summary-detail-icon {
        width: 32px;
        height: 32px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .summary-detail-icon.orange {
        background: #fff7ed;
        color: #ff5a1f;
    }

    .summary-detail-icon.green {
        background: #dcfce7;
        color: #16a34a;
    }

    .summary-detail-icon.blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .summary-detail-label {
        color: #0f172a;
        font-weight: 700;
    }

    .summary-detail-value {
        color: #475569;
        font-weight: 500;
        text-align: right;
    }

    .summary-lines {
        border-top: 1px solid #e5e7eb;
        padding-top: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 14px;
        color: #475569;
        font-weight: 500;
    }

    .summary-line.total {
        border-top: 1px solid #e5e7eb;
        padding-top: 12px;
        margin-top: 4px;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .summary-line.total span:last-child {
        color: #ff5a1f;
        font-size: 24px;
        font-weight: 800;
    }

    .secure-box {
        margin-top: 16px;
        padding: 14px;
        border-radius: 16px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .secure-icon {
        width: 38px;
        height: 38px;
        border-radius: 13px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .secure-title {
        color: #1d4ed8;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .secure-text {
        color: #334155;
        font-size: 12px;
        line-height: 1.5;
        font-weight: 500;
    }

    .payment-alert {
        margin-bottom: 14px;
        border-radius: 14px;
        padding: 14px 15px;
        border: 1px solid #e5e7eb;
        font-size: 13px;
        line-height: 1.6;
    }

    .payment-alert-error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .payment-alert-warning {
        background: #fff7ed;
        border-color: #fdba74;
        color: #c2410c;
    }

    .error-modal {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 50;
        padding: 16px;
    }

    .error-modal-box {
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
        padding: 26px 24px;
        max-width: 360px;
        width: 100%;
        text-align: center;
    }

    .error-modal-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .error-modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .error-modal-text {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 18px;
    }

    .error-modal-btn {
        min-height: 42px;
        padding: 0 18px;
        border: none;
        border-radius: 10px;
        background: #2563eb;
        color: #ffffff;
        font-weight: 700;
    }

    @media (max-width: 1100px) {
        .payment-grid {
            grid-template-columns: 1fr;
        }

        .summary-card {
            position: static;
        }
    }

    @media (max-width: 899px) {
        .payment-shell {
            padding: 18px 14px 40px;
        }

        .payment-title {
            font-size: 28px;
        }

        .payment-method-grid {
            grid-template-columns: 1fr;
        }

        .payment-option {
            min-height: 150px;
        }

        .payment-split {
            grid-template-columns: 1fr;
        }

        .summary-car {
            align-items: flex-start;
        }

        .bank-row {
            grid-template-columns: 36px 1fr;
        }

        .bank-row span:last-child {
            grid-column: 2;
            text-align: left;
        }

        .summary-detail-item {
            grid-template-columns: 34px 1fr;
        }

        .summary-detail-value {
            grid-column: 2;
            text-align: left;
        }
    }
</style>
@endsection

@section('content')
<div class="payment-page">
    <div class="payment-shell">
        <div class="payment-header">
            <a href="{{ !empty($returnIssue) ? route('user.return-issues.show', $returnIssue->id) : route('user.pending-payments') }}" class="payment-back" aria-label="Back">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>
                <h1 class="payment-title">{{ $paymentTitle ?? 'Payment' }}</h1>
                <p class="payment-header-subtitle">
                    Choose your preferred payment method and upload your payment proof.
                </p>
            </div>
        </div>

        @php
            $gcashLogoUrl = asset('storage/cars/gcash-logo.webp');

            $rentalDays = max(
                1,
                \Carbon\Carbon::parse($booking->pickup_at)->diffInDays(\Carbon\Carbon::parse($booking->return_at))
            );

            $rentalCost = $booking->car->price_per_day * $rentalDays;
            $pointsDiscount = (float) ($booking->discount_amount ?? 0);

            $total = $booking->final_total !== null
                ? (float) $booking->final_total
                : max(0, $rentalCost - $pointsDiscount);

            $fallbackImage = asset('images/no-car-image.png');

            $buildImageUrl = function ($path) use ($fallbackImage) {
                if (! is_string($path) || empty($path)) {
                    return null;
                }

                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    return $path;
                }

                $cleanPath = ltrim(str_replace('storage/', '', $path), '/');

                return config('filesystems.default') === 's3'
                    ? \Illuminate\Support\Facades\Storage::disk('s3')->url($cleanPath)
                    : asset('storage/' . $cleanPath);
            };

            $carImages = [];

            if (!empty($booking->car->images)) {
                $decodedCarImages = json_decode($booking->car->images, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedCarImages)) {
                    $carImages = $decodedCarImages;
                }
            }

            $firstCarImage = $carImages[0] ?? null;
            $carImageUrl = $buildImageUrl($firstCarImage) ?? $fallbackImage;

            $gcashQrUrl = $paymentSetting && $paymentSetting->gcash_qr_image
                ? ($buildImageUrl($paymentSetting->gcash_qr_image) ?? asset('images/no-image.png'))
                : asset('images/no-image.png');
        @endphp

        <div class="payment-grid">
            <div style="display:flex; flex-direction:column; gap:18px;">

                @if($errors->any())
                    <div class="payment-alert payment-alert-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(($paymentType ?? 'booking') === 'return_issue' && !empty($returnIssue))
                    <div class="payment-alert payment-alert-warning">
                        You are paying for a return issue: <strong>{{ $returnIssue->title }}</strong>.
                        Please upload your proof of payment after sending the exact final charge.
                    </div>
                @endif

                <div class="payment-card">
                    <div class="payment-card-head">
                        <div class="payment-card-head-icon">
                            <i class="fas fa-wallet"></i>
                        </div>

                        <div>
                            <h2 class="payment-card-title">1. Select Payment Method</h2>
                            <p class="payment-card-subtitle">
                                Choose how you want to pay.
                            </p>
                        </div>
                    </div>

                    <div class="payment-method-grid">
                        <label class="payment-option" data-method="bank">
                            <input type="radio" name="payment_method" class="hidden" hidden>
                            <span class="payment-radio-dot"></span>

                            <span class="payment-option-icon bank-icon">
                                <i class="fas fa-university"></i>
                            </span>

                            <span class="payment-option-label">Bank Transfer</span>
                            <span class="payment-option-note">Transfer via your bank</span>
                        </label>

                        <label class="payment-option" data-method="gcash">
                            <input type="radio" name="payment_method" class="hidden" hidden>
                            <span class="payment-radio-dot"></span>

                            <span class="payment-option-icon gcash-icon">
                                <img src="{{ $gcashLogoUrl }}" alt="GCash" class="gcash-logo-img">
                            </span>

                            <span class="payment-option-label">GCash</span>
                            <span class="payment-option-note">Pay using GCash</span>
                        </label>
                    </div>
                </div>

                <form id="paymentForm" action="{{ route('user.payment.process') }}" method="POST" enctype="multipart/form-data" class="payment-form">
                    @csrf

                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <input type="hidden" name="return_issue_id" value="{{ $returnIssue->id ?? '' }}">
                    <input type="hidden" name="payment_method" id="paymentMethod">

                    <div id="gcashSection" class="payment-card payment-panel hidden-panel">
                        <div class="payment-panel-head">
                            <div class="payment-panel-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>

                            <div>
                                <h3 class="payment-panel-title">GCash QR Payment</h3>
                                <p class="payment-panel-subtitle">
                                    Scan the QR code using your GCash app and upload your payment screenshot.
                                </p>
                            </div>
                        </div>

                        <div class="payment-split">
                            <div>
                                <div class="provider-row">
                                    <div class="provider-badge">
                                        <img src="{{ $gcashLogoUrl }}" alt="GCash">
                                    </div>

                                    <div>
                                        <div class="provider-name">
                                            {{ $paymentSetting->gcash_account_name ?? 'N/A' }}
                                        </div>

                                        <div class="upload-subtitle" style="margin-bottom:0;">
                                            GCash Number: {{ $paymentSetting->gcash_number ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="divider-line"></div>

                                <div class="upload-title">Upload Payment Receipt</div>
                                <div class="upload-subtitle">
                                    Upload a screenshot of your payment. JPG, PNG or JPEG only.
                                </div>

                                <div class="upload-box">
                                    <label>
                                        <div class="upload-box-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>

                                        <div class="upload-main">Click to upload</div>
                                        <div class="upload-help">Tap to choose your GCash receipt image</div>

                                        <input type="file" id="gcashInput" name="receipt_image" accept="image/*" class="hidden" hidden>
                                    </label>

                                    <div id="gcashPreview" class="preview-box">
                                        <img id="gcashPreviewImg" alt="GCash receipt preview">
                                    </div>
                                </div>
                            </div>

                            <div class="payment-qr-wrap">
                                <img
                                    src="{{ $gcashQrUrl }}"
                                    alt="GCash QR"
                                    onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';"
                                >

                                <div class="payment-qr-name">
                                    {{ $paymentSetting->gcash_account_name ?? 'N/A' }}
                                </div>

                                <div class="payment-qr-number">
                                    GCash Number: {{ $paymentSetting->gcash_number ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="bankSection" class="payment-card payment-panel hidden-panel">
                        <div class="payment-panel-head">
                            <div class="payment-panel-icon">
                                <i class="fas fa-university"></i>
                            </div>

                            <div>
                                <h3 class="payment-panel-title">Bank Transfer</h3>
                                <p class="payment-panel-subtitle">
                                    Send payment to the bank account below and upload your receipt after payment.
                                </p>
                            </div>
                        </div>

                        <div class="bank-details">
                            <div class="bank-row">
                                <span class="bank-row-icon"><i class="fas fa-university"></i></span>
                                <span>Bank Name</span>
                                <span>BDO</span>
                            </div>

                            <div class="bank-row">
                                <span class="bank-row-icon"><i class="fas fa-user"></i></span>
                                <span>Account Name</span>
                                <span>Car Rental PH</span>
                            </div>

                            <div class="bank-row">
                                <span class="bank-row-icon"><i class="fas fa-credit-card"></i></span>
                                <span>Account Number</span>
                                <span>1234 5678 9012</span>
                            </div>
                        </div>

                        <div class="upload-title">Upload Bank Receipt</div>
                        <div class="upload-subtitle">
                            Upload screenshot after sending bank transfer.
                        </div>

                        <div class="upload-box">
                            <label>
                                <div class="upload-box-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>

                                <div class="upload-main">Click to upload</div>
                                <div class="upload-help">Tap to choose your bank receipt image</div>

                                <input type="file" name="bank_receipt" accept="image/*" id="bankInput" class="hidden" hidden>
                            </label>

                            <div id="bankPreview" class="preview-box">
                                <img id="bankPreviewImg" alt="Bank receipt preview">
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitPaymentBtn" class="submit-payment-btn">
                        <i class="fas fa-lock" style="margin-right:8px;"></i>
                        Submit Payment Proof
                    </button>
                </form>
            </div>

            <div class="summary-card">
                <div class="payment-card">
                    <div class="summary-head">
                        <div class="summary-head-icon">
                            <i class="fas fa-receipt"></i>
                        </div>

                        <div>
                            <h2 class="summary-title">Booking Summary</h2>
                            <p class="summary-subtitle">Please review your booking details.</p>
                        </div>
                    </div>

                    <div class="summary-car">
                        <img
                            src="{{ $carImageUrl }}"
                            alt="{{ $booking->car->model }}"
                            onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                        >

                        <div>
                            <div class="summary-car-name">
                                {{ $booking->car->brand->name ?? 'N/A' }} {{ $booking->car->model }}
                            </div>

                            <div class="summary-car-meta">
                                {{ $booking->car->category->name ?? 'Car Rental' }}
                                • {{ $booking->car->transmission->type ?? 'N/A' }}
                                • {{ $booking->car->fuelType->type ?? 'N/A' }}
                            </div>

                            <div class="summary-pill">
                                <i class="fas fa-users"></i>
                                {{ $booking->car->seats ?? 'N/A' }} Seats
                            </div>
                        </div>
                    </div>

                    <div class="summary-detail-list">
                        <div class="summary-detail-item">
                            <div class="summary-detail-icon orange">
                                <i class="fas fa-calendar-check"></i>
                            </div>

                            <div class="summary-detail-label">Pick-up</div>

                            <div class="summary-detail-value">
                                {{ \Carbon\Carbon::parse($booking->pickup_at)->format('M d, Y • h:i A') }}
                            </div>
                        </div>

                        <div class="summary-detail-item">
                            <div class="summary-detail-icon green">
                                <i class="fas fa-calendar-alt"></i>
                            </div>

                            <div class="summary-detail-label">Return</div>

                            <div class="summary-detail-value">
                                {{ \Carbon\Carbon::parse($booking->return_at)->format('M d, Y • h:i A') }}
                            </div>
                        </div>

                        <div class="summary-detail-item">
                            <div class="summary-detail-icon blue">
                                <i class="fas fa-clock"></i>
                            </div>

                            <div class="summary-detail-label">Duration</div>

                            <div class="summary-detail-value">
                                {{ $rentalDays }} day{{ $rentalDays > 1 ? 's' : '' }}
                            </div>
                        </div>
                    </div>

                    <div class="summary-lines">
                        @if(($paymentType ?? 'booking') === 'return_issue' && !empty($returnIssue))
                            <div class="summary-line">
                                <span>Payment For :</span>
                                <span>Return Issue</span>
                            </div>

                            <div class="summary-line">
                                <span>Issue :</span>
                                <span>{{ $returnIssue->title }}</span>
                            </div>

                            <div class="summary-line">
                                <span>Status :</span>
                                <span>{{ optional($returnIssue->issueStatus)->label ?? ucfirst(str_replace('_', ' ', $returnIssue->status ?? '')) }}</span>
                            </div>
                        @else
                            <div class="summary-line">
                                <span>Payment For :</span>
                                <span>Booking</span>
                            </div>
                        @endif

                        <div class="summary-line total">
                            <span>Total Amount :</span>
                            <span>₱{{ number_format($payableAmount ?? $total, 2) }}</span>
                        </div>
                    </div>

                    <div class="secure-box">
                        <div class="secure-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>

                        <div>
                            <div class="secure-title">Your booking is secure</div>
                            <div class="secure-text">
                                Your payment will be reviewed before your booking is confirmed.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="errorModal" class="error-modal">
            <div class="error-modal-box">
                <div class="error-modal-icon">⚠️</div>

                <div class="error-modal-title">Upload Required</div>

                <div class="error-modal-text">
                    Please upload your payment receipt before submitting.
                </div>

                <button onclick="closeModal()" class="error-modal-btn">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/user/userpayment.js') }}"></script>
@endsection