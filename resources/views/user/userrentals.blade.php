@extends('layouts.userlayout')

@section('custom-styles')
    <style>
        .rentals-page {
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Outfit', sans-serif;
        }

        .rentals-shell {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 18px 42px;
        }

        .rentals-header {
            margin-bottom: 16px;
        }

        .rentals-header h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.1;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .rentals-tabs-wrap {
            margin-bottom: 24px;
        }

        .rentals-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .rentals-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 20px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            color: #334155;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            transition: all 0.25s ease;
        }

        .rentals-tab:hover {
            color: #ff5a1f;
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .rentals-tab.is-active {
            background: #ff5a1f;
            color: #ffffff;
            border-color: #ff5a1f;
            box-shadow: 0 8px 20px rgba(255, 90, 31, 0.20);
        }

        .rentals-content {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .rental-card {
            background: #ffffff;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .rental-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        }

        .rental-card-inner {
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 900px) {
            .rental-card-inner {
                flex-direction: row;
            }
        }

        .rental-image-wrap {
            position: relative;
            flex: 0 0 330px;
            min-height: 210px;
            background: #e2e8f0;
        }

        .rental-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .rental-body {
            flex: 1;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 14px;
        }

        .rental-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .rental-title {
            margin: 0 0 6px;
            font-size: 22px;
            line-height: 1.15;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .rental-subtitle {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
        }

        .rental-price {
            text-align: right;
        }

        .rental-price strong {
            display: block;
            font-size: 22px;
            line-height: 1;
            font-weight: 800;
            color: #0f172a;
        }

        .rental-price span {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
        }

        .rental-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #fff7ed;
            color: #ff5a1f;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            border: 1px solid #fed7aa;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .rental-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 16px;
        }

        .rental-meta-card {
            border-radius: 12px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rental-meta-icon {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: #fff7ed;
            color: #ff5a1f;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 13px;
            border: 1px solid #fed7aa;
        }

        .rental-meta-card p {
            margin: 0;
            line-height: 1.2;
        }

        .rental-meta-label {
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .rental-meta-value {
            font-size: 14px;
            color: #0f172a;
            font-weight: 700;
        }

        .rental-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .rental-btn,
        .rental-btn-secondary,
        .rental-btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            border: none;
        }

        .rental-btn {
            background: #2563eb;
            color: #ffffff;
        }

        .rental-btn:hover {
            background: #1d4ed8;
            color: #ffffff;
        }

        .rental-btn-secondary {
            background: #ffffff;
            color: #dc2626;
            border: 1.5px solid #fecaca;
        }

        .rental-btn-secondary:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        .rental-btn-disabled {
            background: #e5e7eb;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .rental-alert-box {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .rental-alert-title {
            margin: 0 0 6px;
            font-size: 12px;
            font-weight: 800;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .rental-alert-text {
            margin: 0;
            font-size: 14px;
            color: #7f1d1d;
            font-weight: 500;
        }

        .empty-rentals {
            background: #ffffff;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            padding: 48px 24px;
            text-align: center;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.05);
        }

        .empty-rentals p {
            margin: 0 0 20px;
            font-size: 16px;
            color: #64748b;
            font-weight: 500;
        }

        .browse-link-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 10px;
            background: #ff5a1f;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
        }

        .browse-link-btn:hover {
            color: #ffffff;
            background: #ea580c;
        }

        .rentals-pagination {
            margin-top: 10px;
        }

        .rentals-pagination nav {
            display: flex;
            justify-content: center;
        }

        .rentals-pagination .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .rentals-pagination .page-link,
        .rentals-pagination span,
        .rentals-pagination a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 44px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            font-weight: 700;
        }

        .rentals-pagination .active span,
        .rentals-pagination .page-item.active .page-link {
            background: #ff5a1f;
            border-color: #ff5a1f;
            color: #ffffff;
        }

        @media (max-width: 899px) {
            .rentals-shell {
                padding: 18px 14px 40px;
            }

            .rentals-header h1 {
                font-size: 28px;
            }

            .rental-image-wrap {
                min-height: 220px;
                flex-basis: auto;
            }

            .rental-body {
                padding: 18px 16px;
            }

            .rental-title {
                font-size: 20px;
            }

            .rental-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .rental-price {
                text-align: left;
            }

            .rental-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="rentals-page">
        <div class="rentals-shell">

            <div class="rentals-header">
                <h4>My Rentals</h4>
            </div>

            <div class="rentals-tabs-wrap">
                <div class="rentals-tabs">
                    <a href="{{ route('user.rentals.active') }}"
                        class="rentals-tab {{ request()->routeIs('user.rentals.active') ? 'is-active' : '' }}">
                        Active Rentals
                    </a>

                    <a href="{{ route('user.rentals.pending') }}"
                        class="rentals-tab {{ request()->routeIs('user.rentals.pending') ? 'is-active' : '' }}">
                        Pending
                    </a>

                    <a href="{{ route('user.rentals.upcoming') }}"
                        class="rentals-tab {{ request()->routeIs('user.rentals.upcoming') ? 'is-active' : '' }}">
                        Reserved
                    </a>

                    <a href="{{ route('user.rentals.completed') }}"
                        class="rentals-tab {{ request()->routeIs('user.rentals.completed') ? 'is-active' : '' }}">
                        Completed
                    </a>

                    <a href="{{ route('user.rentals.cancelled') }}"
                        class="rentals-tab {{ request()->routeIs('user.rentals.cancelled') ? 'is-active' : '' }}">
                        Cancelled
                    </a>

                    <a href="{{ route('user.rentals.failed') }}"
                        class="rentals-tab {{ request()->routeIs('user.rentals.failed') ? 'is-active' : '' }}">
                        Failed
                    </a>
                </div>
            </div>

            <div class="rentals-content">
                @forelse ($bookings as $booking)
                    @php
                        $images = [];

                        if ($booking->car && is_array($booking->car->images)) {
                            $images = $booking->car->images;
                        } elseif ($booking->car && is_string($booking->car->images)) {
                            $decodedImages = json_decode($booking->car->images, true);

                            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                                $images = $decodedImages;
                            } elseif (!empty($booking->car->images)) {
                                $images = [$booking->car->images];
                            }
                        }

                        $firstImage = $images[0] ?? null;
                        $fallbackImage = asset('images/no-car-image.png');
                        $imageUrl = $fallbackImage;

                        if (is_string($firstImage) && !empty($firstImage)) {
                            if (filter_var($firstImage, FILTER_VALIDATE_URL)) {
                                $imageUrl = $firstImage;
                            } else {
                                $cleanImagePath = ltrim(str_replace('storage/', '', $firstImage), '/');

                                $imageUrl = config('filesystems.default') === 's3'
                                    ? \Illuminate\Support\Facades\Storage::disk('s3')->url($cleanImagePath)
                                    : asset('storage/' . $cleanImagePath);
                            }
                        }
                    @endphp

                    <div class="rental-card">
                        <div class="rental-card-inner">

                            <div class="rental-image-wrap">
                                <img src="{{ $imageUrl }}"
                                    alt="{{ $booking->car->model ?? 'Car image' }}" class="rental-image"
                                    onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                            </div>

                            <div class="rental-body">
                                <div>
                                    <div class="rental-top">
                                        <div>
                                            <h3 class="rental-title">
                                                {{ $booking->car->brand->name ?? 'N/A' }} {{ $booking->car->model ?? '' }}
                                            </h3>
                                            <p class="rental-subtitle">
                                                {{ $booking->car->fuelType->type ?? 'N/A' }} • {{ $booking->car->transmission->type ?? 'N/A' }}
                                            </p>
                                        </div>

                                        <div class="rental-price">
                                            <strong>₱{{ number_format($booking->car->price_per_day ?? 0) }}</strong>
                                            <span>/day</span>
                                        </div>
                                    </div>

                                    <div class="rental-meta-grid">
                                        <div class="rental-meta-card">
                                            <div class="rental-meta-icon">📅</div>
                                            <div>
                                                <p class="rental-meta-label">Pick-up</p>
                                                <p class="rental-meta-value">
                                                    {{ $booking->pickup_at ? $booking->pickup_at->format('M d, Y') : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="rental-meta-card">
                                            <div class="rental-meta-icon">📅</div>
                                            <div>
                                                <p class="rental-meta-label">Return</p>
                                                <p class="rental-meta-value">
                                                    {{ $booking->return_at ? $booking->return_at->format('M d, Y') : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="rental-meta-card">
                                            <div class="rental-meta-icon">⏱️</div>
                                            <div>
                                                <p class="rental-meta-label">Duration</p>
                                                <p class="rental-meta-value">
                                                    {{ $booking->pickup_at && $booking->return_at ? max(1, round($booking->pickup_at->diffInDays($booking->return_at))) : 0 }}
                                                    days
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if(($booking->status->name ?? '') === 'Failed' && $booking->photoReceipt?->admin_note)
                                        <div class="rental-alert-box">
                                            <p class="rental-alert-title">Payment Rejection Reason</p>
                                            <p class="rental-alert-text">
                                                {{ $booking->photoReceipt->admin_note }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="rental-actions">
                                    @if(($booking->status->name ?? '') !== 'Cancelled' && ($booking->status->name ?? '') !== 'Failed' && $booking->pickup_at && $booking->pickup_at > now()->addHours(24))
                                        <form action="{{ route('user.booking.cancel', $booking->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            @csrf
                                            <button type="submit" class="rental-btn-secondary">
                                                Cancel Booking
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="rental-btn-disabled">
                                            Cancel Booking
                                        </button>
                                    @endif

                                    @if(($booking->status->name ?? '') === 'Failed')
                                        <form action="{{ route('user.booking.retry', $booking->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="rental-btn">
                                                Upload New Receipt
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="empty-rentals">
                        <p>No bookings yet. Start by browsing available cars!</p>
                        <a href="{{ route('user.browse') }}" class="browse-link-btn">
                            Browse Cars
                        </a>
                    </div>
                @endforelse

                @if ($bookings->hasPages())
                    <div class="rentals-pagination">
                        {{ $bookings->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection