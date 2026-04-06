@extends('layouts.userlayout')

@section('custom-styles')
<style>
    .receipt-page {
        min-height: 100vh;
        background: #f8fafc;
        font-family: 'Outfit', sans-serif;
    }

    .receipt-shell {
        max-width: 680px;
        margin: 0 auto;
        padding: 18px 14px 32px;
    }

    .receipt-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        padding: 20px 25px 10px;
    }

    .receipt-title {
        margin: 0 0 18px;
        text-align: center;
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
    }

    .receipt-block + .receipt-block {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #f1f5f9;
    }

    .receipt-block-title {
        margin: 0 0 10px;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .receipt-line {
        display: flex;
        justify-content: space-between;
        gap: 5px;
        padding: 3px 4;
        font-size: 13px;
        border-bottom: -5px solid #f8fafc;
    }

    .receipt-line:last-child {
        border-bottom: none;
    }

    .receipt-label {
        color: #64748b;
        font-weight: 500;
    }

    .receipt-value {
        color: #0f172a;
        font-weight: 700;
        text-align: right;
    }

    .receipt-value.amount {
        color: #16a34a;
        font-size: 20px;
        font-weight: 800;
    }

    .receipt-status {
        color: #16a34a;
        font-weight: 700;
        font-size: 13px;
    }

    .receipt-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }

    .receipt-btn,
    .receipt-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        padding: 0 14px;
        transition: all 0.2s ease;
    }

    .receipt-btn {
        background: #2563eb;
        color: #ffffff;
    }

    .receipt-btn:hover {
        background: #1d4ed8;
        color: #ffffff;
    }

    .receipt-btn-secondary {
        background: #475569;
        color: #ffffff;
    }

    .receipt-btn-secondary:hover {
        background: #334155;
        color: #ffffff;
    }

    @media (max-width: 640px) {
        .receipt-actions {
            grid-template-columns: 1fr;
        }

        .receipt-line {
            flex-direction: column;
            gap: 4px;
        }

        .receipt-value {
            text-align: left;
        }
    }
</style>
@endsection

@section('content')
<div class="receipt-page">
    <div class="receipt-shell">

        <div class="receipt-card">
            <h1 class="receipt-title">Receipt</h1>

            <div class="receipt-block">
                <h3 class="receipt-block-title">Booking Details</h3>

                <div class="receipt-line">
                    <span class="receipt-label">Booking ID</span>
                    <span class="receipt-value">#{{ $booking->id }}</span>
                </div>

                <div class="receipt-line">
                    <span class="receipt-label">Car</span>
                    <span class="receipt-value">{{ $booking->car->brand->name }} {{ $booking->car->model }}</span>
                </div>

                <div class="receipt-line">
                    <span class="receipt-label">Pickup Date</span>
                    <span class="receipt-value">{{ $booking->pickup_at }}</span>
                </div>

                <div class="receipt-line">
                    <span class="receipt-label">Return Date</span>
                    <span class="receipt-value">{{ $booking->return_at }}</span>
                </div>
            </div>

            @if($payment)
                <div class="receipt-block">
                    <h3 class="receipt-block-title">Payment Details</h3>

                    <div class="receipt-line">
                        <span class="receipt-label">Amount</span>
                        <span class="receipt-value amount">₱{{ number_format($payment->amount, 2) }}</span>
                    </div>

                    <div class="receipt-line">
                        <span class="receipt-label">Payment Method</span>
                        <span class="receipt-value">{{ $payment->paymentMethod->name }}</span>
                    </div>

                    <div class="receipt-line">
                        <span class="receipt-label">Status</span>
                        <span class="receipt-status">{{ $payment->paymentStatus->name }}</span>
                    </div>
                </div>
            @endif

            @if($receipt)
                <div class="receipt-block">
                    <h3 class="receipt-block-title">Receipt Information</h3>

                    <div class="receipt-line">
                        <span class="receipt-label">Receipt Number</span>
                        <span class="receipt-value">{{ $receipt->receipt_number }}</span>
                    </div>

                    <div class="receipt-line">
                        <span class="receipt-label">Generated</span>
                        <span class="receipt-value">{{ $receipt->generated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="receipt-actions">
            <a href="{{ route('user.rentals.index') }}" class="receipt-btn">
                View My Rentals
            </a>

            <a href="{{ route('user.browse') }}" class="receipt-btn-secondary">
                Browse More Cars
            </a>
        </div>

    </div>
</div>
@endsection