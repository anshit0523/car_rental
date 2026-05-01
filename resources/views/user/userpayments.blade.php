@extends('layouts.userlayout')

@section('custom-styles')
<style>
    .payment-page {
        min-height: 100vh;
        background: #f8fafc;
        font-family: 'Outfit', sans-serif;
    }

    .payment-shell {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px 18px 48px;
    }

    .payment-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }

    .payment-back {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #0f172a;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .payment-title {
        margin: 0;
        font-size: 30px;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .payment-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(320px, 420px);
        gap: 24px;
        align-items: start;
    }

    .payment-card {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        padding: 22px;
    }

    .payment-card-title {
        margin: 0 0 14px;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .payment-card-subtitle {
        margin: 0 0 16px;
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        font-weight: 500;
    }

    .payment-method-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .payment-option {
        border: 1.5px solid #e5e7eb;
        border-radius: 16px;
        padding: 18px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        transition: all 0.25s ease;
    }

    .payment-option:hover {
        border-color: #fed7aa;
        background: #fff7ed;
    }

    .payment-option.is-active {
        border-color: #ff5a1f;
        background: #fff7ed;
        box-shadow: 0 10px 24px rgba(255, 90, 31, 0.10);
    }

    .payment-option-icon {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        font-size: 18px;
        flex-shrink: 0;
    }

    .payment-option-label {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .payment-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .payment-panel {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 20px;
        background: #ffffff;
    }

    .payment-panel.hidden-panel {
        display: none;
    }

    .payment-panel-title {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    .payment-panel-subtitle {
        margin: 0 0 18px;
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }

    .payment-split {
        display: grid;
        grid-template-columns: 1fr 260px;
        gap: 22px;
        align-items: center;
    }

    .provider-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .provider-badge {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        background: #dbeafe;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    .provider-name {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .divider-line {
        height: 1px;
        background: #e5e7eb;
        margin: 14px 0 16px;
    }

    .upload-title {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .upload-subtitle {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 12px;
    }

    .upload-box {
        border: 1.5px dashed #cbd5e1;
        border-radius: 16px;
        padding: 16px;
        background: #f8fafc;
        transition: all 0.25s ease;
    }

    .upload-box:hover {
        border-color: #94a3b8;
        background: #f1f5f9;
    }

    .upload-box label {
        display: block;
        cursor: pointer;
    }

    .upload-main {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .upload-help {
        font-size: 12px;
        color: #64748b;
    }

    .preview-box {
        margin-top: 12px;
        display: none;
    }

    .preview-box img {
        width: 160px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .payment-qr-wrap {
        text-align: center;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px;
    }

    .payment-qr-wrap img {
        width: 180px;
        max-width: 100%;
        margin: 0 auto 12px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid #e5e7eb;
        padding: 8px;
        display: block;
    }

    .payment-qr-name {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .payment-qr-number {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }

    .bank-details {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .bank-row {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .bank-row:last-child {
        margin-bottom: 0;
    }

    .bank-row span:first-child {
        color: #64748b;
        font-weight: 600;
    }

    .bank-row span:last-child {
        color: #0f172a;
        font-weight: 800;
        text-align: right;
    }

    .file-input {
        width: 100%;
        min-height: 44px;
        border: 1px solid #dbe2ea;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 13px;
        background: #fff;
    }

    .submit-payment-btn {
        width: 100%;
        min-height: 48px;
        border: none;
        border-radius: 12px;
        background: #2563eb;
        color: #ffffff;
        font-size: 15px;
        font-weight: 800;
        transition: all 0.25s ease;
    }

    .submit-payment-btn:hover {
        background: #1d4ed8;
    }

    .submit-payment-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .summary-card {
        position: sticky;
        top: 110px;
    }

    .summary-title {
        margin: 0 0 14px;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
    }

    .summary-car {
        display: flex;
        gap: 14px;
        margin-bottom: 18px;
        align-items: center;
    }

    .summary-car img {
        width: 84px;
        height: 84px;
        border-radius: 16px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid #e5e7eb;
    }

    .summary-car-name {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .summary-car-meta {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        line-height: 1.5;
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
        font-weight: 600;
    }

    .summary-line.total {
        border-top: 1px solid #e5e7eb;
        padding-top: 12px;
        margin-top: 4px;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
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
        font-weight: 800;
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
        font-weight: 800;
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

        .payment-split {
            grid-template-columns: 1fr;
        }

        .summary-car {
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
<div class="payment-page">
    <div class="payment-shell">
        <div class="payment-header">
            <a href="{{ !empty($returnIssue) ? route('user.return-issues.show', $returnIssue->id) : route('user.pending-payments') }}" class="payment-back" aria-label="Back">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <h1 class="payment-title">{{ $paymentTitle ?? 'Payment' }}</h1>
        </div>

        @php
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
            <!-- Left -->
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
                    <h2 class="payment-card-title">Select Payment Method</h2>
                    <p class="payment-card-subtitle">
                        Choose your preferred payment option, then upload your receipt after sending the payment.
                    </p>

                    <div class="payment-method-grid">
                        <label class="payment-option" data-method="gcash">
                            <input type="radio" name="payment_method" class="hidden" hidden>
                            <span class="payment-option-icon">💙</span>
                            <span class="payment-option-label">GCash</span>
                        </label>

                        <label class="payment-option" data-method="bank">
                            <input type="radio" name="payment_method" class="hidden" hidden>
                            <span class="payment-option-icon">🏦</span>
                            <span class="payment-option-label">Bank Transfer</span>
                        </label>
                    </div>
                </div>

                <form id="paymentForm" action="{{ route('user.payment.process') }}" method="POST" enctype="multipart/form-data" class="payment-form">
                    @csrf

                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    <input type="hidden" name="return_issue_id" value="{{ $returnIssue->id ?? '' }}">
                    <input type="hidden" name="payment_method" id="paymentMethod">

                    <!-- GCASH -->
                    <div id="gcashSection" class="payment-card payment-panel hidden-panel">
                        <h3 class="payment-panel-title">GCash QR Payment</h3>
                        <p class="payment-panel-subtitle">Scan the QR code using your GCash app and upload your payment screenshot.</p>

                        <div class="payment-split">
                            <div>
                                <div class="provider-row">
                                    <div class="provider-badge">G</div>
                                    <div class="provider-name">Car Rental PH</div>
                                </div>

                                <div class="divider-line"></div>

                                <div class="upload-title">Upload Payment Receipt</div>
                                <div class="upload-subtitle">Upload a screenshot of your payment. JPG, PNG or JPEG only.</div>

                                <div class="upload-box">
                                    <label>
                                        <div class="upload-main">Upload Payment Screenshot</div>
                                        <div class="upload-help">Tap to choose your receipt image</div>

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

                    <!-- BANK -->
                    <div id="bankSection" class="payment-card payment-panel hidden-panel">
                        <h3 class="payment-panel-title">Bank Transfer</h3>
                        <p class="payment-panel-subtitle">Send payment to the bank account below and upload your receipt after payment.</p>

                        <div class="bank-details">
                            <div class="bank-row">
                                <span>Bank</span>
                                <span>BDO</span>
                            </div>

                            <div class="bank-row">
                                <span>Account Name</span>
                                <span>Car Rental PH</span>
                            </div>

                            <div class="bank-row">
                                <span>Account Number</span>
                                <span>1234 5678 9012</span>
                            </div>
                        </div>

                        <div class="upload-title">Upload Bank Receipt</div>
                        <input type="file" name="bank_receipt" accept="image/*" id="bankInput" class="file-input">

                        <div id="bankPreview" class="preview-box">
                            <img id="bankPreviewImg" alt="Bank receipt preview">
                        </div>

                        <div class="upload-subtitle" style="margin-top:10px;">
                            Upload screenshot after sending bank transfer.
                        </div>
                    </div>

                    <button type="submit" id="submitPaymentBtn" class="submit-payment-btn">
                        Submit Payment Proof
                    </button>
                </form>
            </div>

            <!-- Right -->
            <div class="summary-card">
                <div class="payment-card">
                    <h2 class="summary-title">Booking Summary</h2>

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
                                {{ \Carbon\Carbon::parse($booking->pickup_at)->format('M d') }}
                                -
                                {{ \Carbon\Carbon::parse($booking->return_at)->format('M d, Y') }}
                            </div>

                            <div class="summary-car-meta">
                                {{ $rentalDays }} days rental
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
                            <span>Total :</span>
                            <span>₱{{ number_format($payableAmount ?? $total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
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