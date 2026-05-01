@extends('layouts.userlayout')

@section('title', 'Payments')

@section('custom-styles')
<style>
    .pending-payments-page {
        padding: 25px 0 50px;
        font-family: 'Outfit', sans-serif;
    }

    .pending-payments-container {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 18px;
    }

    .pending-payments-header {
        margin-bottom: 22px;
    }

    .pending-payments-title {
        font-size: 34px;
        line-height: 1.15;
        font-weight: 800;
        color: #111827;
        margin: 0 0 8px;
    }

    .pending-payments-subtitle {
        font-size: 16px;
        color: #6b7280;
        margin: 0;
    }

    .pending-notice {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 16px;
        padding: 18px 20px;
        margin-bottom: 22px;
    }

    .pending-notice-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ff5a1f;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
    }

    .pending-notice-title {
        font-size: 17px;
        font-weight: 800;
        color: #111827;
        margin: 0 0 4px;
    }

    .pending-notice-text {
        font-size: 14px;
        line-height: 1.5;
        color: #374151;
        margin: 0;
    }

    .pending-card-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .pending-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        padding: 18px;
    }

    .pending-card-inner {
        display: grid;
        grid-template-columns: 170px 1fr 170px;
        gap: 22px;
        align-items: center;
    }

    .pending-car-image-wrap {
        width: 170px;
        height: 105px;
        background: #f8fafc;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .pending-car-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .pending-no-image {
        color: #9ca3af;
        text-align: center;
        font-size: 12px;
    }

    .pending-car-title {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        margin: 0 0 4px;
    }

    .pending-car-meta {
        font-size: 14px;
        color: #6b7280;
        margin: 0 0 14px;
    }

    .pending-divider {
        height: 1px;
        background: #f1f5f9;
        margin-bottom: 14px;
    }

    .pending-info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .pending-info-label {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 800;
        color: #6b7280;
        margin-bottom: 5px;
    }

    .pending-info-value {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .pending-info-sub {
        font-size: 13px;
        color: #374151;
        margin: 2px 0 0;
    }

    .pending-amount {
        font-size: 17px;
        font-weight: 900;
        color: #ff5a1f;
        margin: 0;
    }

    .pending-action {
        border-left: 1px solid #f1f5f9;
        padding-left: 22px;
    }

    .pending-pay-btn {
        width: 100%;
        min-height: 48px;
        border: none;
        border-radius: 12px;
        background: #ff5a1f;
        color: #fff;
        font-size: 15px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        text-decoration: none;
        transition: all 0.25s ease;
    }

    .pending-pay-btn:hover {
        background: #e94d16;
        color: #fff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .pending-booked-date {
        font-size: 12px;
        color: #6b7280;
        text-align: center;
        margin-top: 10px;
    }

    .pending-footer {
        margin-top: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .pending-footer-text {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    .pending-empty {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 45px 20px;
        text-align: center;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .pending-empty-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #fff7ed;
        color: #ff5a1f;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 28px;
    }

    .pending-empty-title {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        margin: 0 0 8px;
    }

    .pending-empty-text {
        color: #6b7280;
        font-size: 15px;
        margin: 0 0 20px;
    }

    .pending-browse-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #ff5a1f;
        color: #fff;
        padding: 12px 22px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
    }

    .pending-browse-btn:hover {
        background: #e94d16;
        color: #fff;
        text-decoration: none;
    }

    @media (max-width: 992px) {
        .pending-card-inner {
            grid-template-columns: 150px 1fr;
        }

        .pending-action {
            grid-column: 1 / -1;
            border-left: none;
            border-top: 1px solid #f1f5f9;
            padding-left: 0;
            padding-top: 16px;
        }

        .pending-car-image-wrap {
            width: 150px;
            height: 100px;
        }
    }

    @media (max-width: 768px) {
        .pending-payments-title {
            font-size: 28px;
        }

        .pending-card-inner {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .pending-car-image-wrap {
            width: 100%;
            height: 150px;
        }

        .pending-info-grid {
            grid-template-columns: 1fr;
        }

        .pending-booked-date {
            text-align: left;
        }
    }
</style>
@endsection

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $buildCarImageUrl = function ($car) {
        if (!$car) {
            return null;
        }

        $images = [];

        if (is_array($car->images)) {
            $images = $car->images;
        } elseif (is_string($car->images)) {
            $decodedImages = json_decode($car->images, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                $images = $decodedImages;
            } elseif (!empty($car->images)) {
                $images = [$car->images];
            }
        }

        $firstImage = $images[0] ?? null;

        if (!$firstImage) {
            return null;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $firstImage), '/');

        return config('filesystems.default') === 's3'
            ? Storage::disk('s3')->url($cleanPath)
            : asset('storage/' . $cleanPath);
    };
@endphp

<div class="pending-payments-page">
    <div class="pending-payments-container">

        <div class="pending-payments-header">
            <h1 class="pending-payments-title">My Pending Payments</h1>
            <p class="pending-payments-subtitle">
                Bookings that are waiting for your payment receipt.
            </p>
        </div>

        <div class="pending-notice">
            <div class="pending-notice-icon">
                <i class="fas fa-info"></i>
            </div>

            <div>
                <h2 class="pending-notice-title">Pending Payment</h2>
                <p class="pending-notice-text">
                    These bookings are saved but not yet paid. Please upload your payment receipt so we can verify and confirm your booking.
                </p>
            </div>
        </div>

        @if($payments->count() > 0)
            <div class="pending-card-list">
                @foreach($payments as $payment)
                    @php
                        $booking = $payment->booking;
                        $car = $booking?->car;

                        $carName = trim(($car?->brand?->name ?? '') . ' ' . ($car?->model ?? ''));
                        $imageUrl = $buildCarImageUrl($car);

                        $transmission = $car?->transmission?->type ?? 'N/A';
                        $fuel = $car?->fuelType?->type ?? 'N/A';
                        $seats = $car?->seats ?? 'N/A';

                        $amount = $payment->amount
                            ?? $booking?->final_total
                            ?? $booking?->total_price
                            ?? 0;
                    @endphp

                    <div class="pending-card">
                        <div class="pending-card-inner">

                            <div class="pending-car-image-wrap">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $carName }}" class="pending-car-image">
                                @else
                                    <div class="pending-no-image">
                                        <i class="fas fa-car fa-2x mb-2"></i>
                                        <div>No Image</div>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h2 class="pending-car-title">
                                    {{ $carName !== '' ? $carName : 'Selected Vehicle' }}
                                </h2>

                                <p class="pending-car-meta">
                                    {{ $transmission }} • {{ $fuel }} • {{ $seats }} Seats
                                </p>

                                <div class="pending-divider"></div>

                                <div class="pending-info-grid">
                                    <div>
                                        <div class="pending-info-label">
                                            <i class="far fa-calendar-alt"></i>
                                            Pick-up
                                        </div>
                                        <p class="pending-info-value">
                                            {{ optional($booking?->pickup_at)->format('M d, Y') ?? 'N/A' }}
                                        </p>
                                        <p class="pending-info-sub">
                                            {{ optional($booking?->pickup_at)->format('h:i A') ?? '' }}
                                        </p>
                                    </div>

                                    <div>
                                        <div class="pending-info-label">
                                            <i class="far fa-calendar-alt"></i>
                                            Return
                                        </div>
                                        <p class="pending-info-value">
                                            {{ optional($booking?->return_at)->format('M d, Y') ?? 'N/A' }}
                                        </p>
                                        <p class="pending-info-sub">
                                            {{ optional($booking?->return_at)->format('h:i A') ?? '' }}
                                        </p>
                                    </div>

                                    <div>
                                        <div class="pending-info-label">
                                            <i class="fas fa-coins"></i>
                                            Total Amount
                                        </div>
                                        <p class="pending-amount">
                                            ₱{{ number_format($amount, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="pending-action">
                                <a href="{{ route('user.payments', ['booking_id' => $booking->id]) }}"
                                   class="pending-pay-btn">
                                    <i class="fas fa-upload"></i>
                                    Pay Now
                                </a>

                                <p class="pending-booked-date">
                                    Booked on {{ optional($payment->created_at)->format('M d, Y') }}
                                </p>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pending-footer">
                <p class="pending-footer-text">
                    Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} bookings
                </p>

                <div>
                    {{ $payments->links() }}
                </div>
            </div>
        @else
            <div class="pending-empty">
                <div class="pending-empty-icon">
                    <i class="fas fa-wallet"></i>
                </div>

                <h2 class="pending-empty-title">No Pending Payments</h2>

                <p class="pending-empty-text">
                    You do not have any saved bookings waiting for payment.
                </p>

                <a href="{{ route('user.browse') }}" class="pending-browse-btn">
                    <i class="fas fa-car-side"></i>
                    Browse Cars
                </a>
            </div>
        @endif

    </div>
</div>
@endsection