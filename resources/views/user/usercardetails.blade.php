@extends('layouts.userlayout')

@section('custom-styles')
<style>

    /* Legend */
.fp-legend{
  display:flex;
  flex-direction:row !important;
  justify-content:center;
  align-items:center;
  gap:14px;
  width:100%;
  padding:10px 12px;
  border-bottom:1px solid #e5e7eb;
  font-size:12px;
  background:#fff;
  white-space:nowrap;
  flex-wrap:nowrap;
  text-align:center;
}

.fp-legend .item{
  display:inline-flex;
  align-items:center;
  gap:8px;
  white-space:nowrap;
  flex:0 0 auto;
  color:#010102;
  font-weight:500;
}

.fp-legend .dot{
  width:12px;
  height:12px;
  border-radius:50%;
  display:inline-block;
}

.fp-legend .dot.red{
  background:#ef4444;
}

.fp-legend .dot.green{
  background:#22c55e;
}

/* Calendar day dots */
.flatpickr-day{
  position:relative;
}

.red-dot,
.green-dot{
  position:absolute;
  bottom:2px;
  left:50%;
  transform:translateX(-50%);
  width:7px;
  height:7px;
  border-radius:50%;
}

.red-dot{
  background:#ef4444;
}

.green-dot{
  background:#22c55e;
}

    .car-details-page {
        min-height: 100vh;
        background: #f8fafc;
        font-family: 'Outfit', sans-serif;
    }

    .car-details-shell {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px 18px 50px;
    }

    .car-breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #64748b;
        margin-bottom: 18px;
        font-weight: 600;
    }

    .car-breadcrumb a {
        color: #64748b;
        text-decoration: none;
    }

    .car-breadcrumb a:hover {
        color: #ff5a1f;
    }

    .car-details-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(320px, 430px);
        gap: 24px;
        align-items: start;
    }

    .car-panel {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .car-main-image {
        width: 100%;
        height: 430px;
        object-fit: cover;
        display: block;
        background: #e2e8f0;
    }

    .car-thumbs {
        display: flex;
        gap: 12px;
        padding: 16px;
        flex-wrap: wrap;
    }

    .car-thumb {
        width: 92px;
        height: 78px;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f1f5f9;
    }

    .car-thumb.active {
        border-color: #ff5a1f;
    }

    .car-thumb:hover {
        opacity: 0.85;
    }

    .car-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .car-header-card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px;
    }

    .car-title {
        margin: 0 0 8px;
        font-size: 30px;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .car-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        font-weight: 600;
    }

    .car-price-box {
        text-align: right;
        flex-shrink: 0;
    }

    .car-price-box strong {
        display: block;
        font-size: 32px;
        line-height: 1;
        color: #0f172a;
        font-weight: 800;
    }

    .car-price-box span {
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
    }

    .section-card {
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        padding: 22px 24px;
        margin-top: 18px;
    }

    .section-title {
        margin: 0 0 16px;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .feature-box {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px 14px;
        text-align: center;
    }

    .feature-icon {
        width: 54px;
        height: 54px;
        border-radius: 999px;
        background: #fff7ed;
        color: #ff5a1f;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 22px;
        border: 1px solid #fed7aa;
    }

    .feature-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .feature-value {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .amenities-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 18px;
    }

    .amenity-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #334155;
        font-size: 14px;
        font-weight: 600;
    }

    .amenity-icon {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        background: #dcfce7;
        color: #16a34a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 12px;
    }

    .terms-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .term-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .term-dot {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        background: #fff7ed;
        color: #ff5a1f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        flex-shrink: 0;
        border: 1px solid #fed7aa;
        margin-top: 1px;
    }

    .term-text {
        font-size: 13px;
        line-height: 1.65;
        color: #475569;
        font-weight: 500;
    }

    .booking-card {
        position: sticky;
        top: 110px;
        background: #ffffff;
        border: 1px solid #eef2f7;
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        padding: 22px 22px 24px;
    }

    .booking-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .booking-top h3 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
        font-weight: 800;
        color: #0f172a;
    }

    .booking-top p {
        margin: 4px 0 0;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .booking-rate {
        text-align: right;
        flex-shrink: 0;
    }

    .booking-rate small {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .booking-rate strong {
        font-size: 22px;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }

    .booking-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .form-group-block {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group-block.full {
        grid-column: span 2;
    }

    .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .form-input,
    .form-select {
        width: 100%;
        min-height: 46px;
        border: 1px solid #dbe2ea;
        border-radius: 12px;
        padding: 0 14px;
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-input:focus,
    .form-select:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 4px rgba(255, 90, 31, 0.08);
    }

    .helper-text {
        font-size: 11px;
        color: #64748b;
        margin-top: 2px;
        font-weight: 500;
    }

    .summary-box {
        border-top: 1px solid #e5e7eb;
        margin-top: 18px;
        padding-top: 16px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-size: 13px;
        color: #475569;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .summary-row strong {
        color: #0f172a;
        font-weight: 800;
    }

    .summary-row.discount strong {
        color: #16a34a;
    }

    .total-box {
        margin-top: 14px;
        margin-bottom: 16px;
    }

    .total-box p {
        margin: 0 0 4px;
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .total-box h4 {
        margin: 0;
        font-size: 28px;
        line-height: 1;
        color: #0f172a;
        font-weight: 800;
    }

    .points-box {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        margin-top: 18px;
        background: #f8fafc;
    }

    .points-box h4 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .points-box p {
        margin: 0;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .points-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 14px;
    }

    .points-btn {
        min-height: 44px;
        padding: 0 16px;
        border: none;
        border-radius: 12px;
        background: #2563eb;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
    }

    .points-btn:hover {
        background: #1d4ed8;
    }

    .agree-wrap {
        margin-top: 18px;
    }

    .agree-label {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
    }

    .agree-label span {
        font-size: 13px;
        line-height: 1.5;
        color: #475569;
        font-weight: 500;
    }

    .agree-label strong {
        color: #0f172a;
    }

    .primary-book-btn {
        width: 100%;
        min-height: 48px;
        border: none;
        border-radius: 12px;
        background: #2563eb;
        color: #ffffff;
        font-size: 15px;
        font-weight: 800;
        margin-top: 16px;
        transition: all 0.25s ease;
    }

    .primary-book-btn:hover {
        background: #1d4ed8;
    }

    .primary-book-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .booking-note {
        text-align: center;
        font-size: 12px;
        color: #64748b;
        margin-top: 10px;
        font-weight: 500;
    }

    .error-box {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 16px;
    }

    .error-box p {
        margin: 0;
        font-size: 12px;
        line-height: 1.5;
        font-weight: 600;
    }

    .inline-message {
        font-size: 12px;
        font-weight: 600;
        margin-top: 6px;
    }

    .inline-message.error {
        color: #dc2626;
    }

    .inline-message.success {
        color: #16a34a;
    }

    @media (max-width: 1100px) {
        .car-details-grid {
            grid-template-columns: 1fr;
        }

        .booking-card {
            position: static;
        }
    }

    @media (max-width: 899px) {
        .car-details-shell {
            padding: 18px 14px 40px;
        }

        .car-main-image {
            height: 280px;
        }

        .car-header-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .car-price-box {
            text-align: left;
        }

        .feature-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .booking-grid {
            grid-template-columns: 1fr;
        }

        .form-group-block.full {
            grid-column: span 1;
        }

        .amenities-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>

<div class="car-details-page">
    <div class="car-details-shell">

        <div class="car-breadcrumb">
            <a href="{{ route('user.browse') }}">Browse Cars</a>
            <span>/</span>
            <span style="color:#0f172a;font-weight:800;">Car Details</span>
        </div>

        <div class="car-details-grid">

            <!-- Left Content -->
            <div>
                <div class="car-panel">
                    <div class="relative">
                        @if($car->images && count(json_decode($car->images)) > 0)
                            @php $images = json_decode($car->images); @endphp
                            <img id="mainCarImage"
                                 src="{{ asset('storage/' . $images[0]) }}"
                                 alt="{{ $car->model }}"
                                 class="car-main-image"
                                 onerror="this.onerror=null;this.src='{{ asset('images/no-car-image.png') }}';">
                        @else
                            <img id="mainCarImage"
                                 src="https://images.unsplash.com/photo-1567818735868-e71b99932e29?w=1200&h=700&fit=crop"
                                 alt="Car"
                                 class="car-main-image">
                        @endif
                    </div>

                    <div class="car-thumbs">
                        @if($car->images && count(json_decode($car->images)) > 0)
                            @php $images = json_decode($car->images); @endphp
                            @foreach($images as $index => $image)
                                <div class="car-thumb {{ $index === 0 ? 'active' : '' }}"
                                     onclick="document.getElementById('mainCarImage').src='{{ asset('storage/' . $image) }}'; document.querySelectorAll('.car-thumb').forEach(el=>el.classList.remove('active')); this.classList.add('active');">
                                    <img src="{{ asset('storage/' . $image) }}"
                                         alt="Thumbnail"
                                         onerror="this.onerror=null;this.src='{{ asset('images/no-car-image.png') }}';">
                                </div>
                            @endforeach
                        @else
                            <span class="helper-text">No images available</span>
                        @endif
                    </div>
                </div>

                <div class="car-header-card section-card" style="margin-top:18px;">
                    <div>
                        <h1 class="car-title">{{ $car->brand->name ?? 'N/A' }} {{ $car->model }}</h1>
                        <p class="car-subtitle">Downtown Location</p>
                    </div>

                    <div class="car-price-box">
                        <strong>₱{{ number_format($car->price_per_day, 0) }}</strong>
                        <span>/day</span>
                    </div>
                </div>

                <div class="section-card">
                    <h3 class="section-title">Key Features</h3>

                    <div class="feature-grid">
                        <div class="feature-box">
                            <div class="feature-icon">🧑‍🤝‍🧑</div>
                            <div class="feature-label">Passengers</div>
                            <div class="feature-value">{{ $car->seats }} People</div>
                        </div>

                        <div class="feature-box">
                            <div class="feature-icon">⚙️</div>
                            <div class="feature-label">Transmission</div>
                            <div class="feature-value">{{ $car->transmission->type ?? 'N/A' }}</div>
                        </div>

                        <div class="feature-box">
                            <div class="feature-icon">⛽</div>
                            <div class="feature-label">Fuel Type</div>
                            <div class="feature-value">{{ $car->fuelType->type ?? 'N/A' }}</div>
                        </div>

                        <div class="feature-box">
                            <div class="feature-icon">🧳</div>
                            <div class="feature-label">Luggage</div>
                            <div class="feature-value">3 Bags</div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <h3 class="section-title">Features & Amenities</h3>

                    @php
                        $amenities = ['Air Conditioning', 'GPS Navigation', 'USB Charging', 'Leather Seats', 'Bluetooth', 'Sunroof'];
                    @endphp

                    <div class="amenities-grid">
                        @foreach($amenities as $a)
                            <div class="amenity-item">
                                <span class="amenity-icon">✓</span>
                                <span>{{ $a }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="section-card">
                    <h3 class="section-title">Rental Terms & Conditions</h3>

                    @php
                        $terms = [
                            'Renter agrees to pay the basic rental fee plus additional charges for excess hours (if any).',
                            'Vehicle must be returned on the agreed return date/time in the same condition (minus normal wear and tear).',
                            'Vehicle must be returned with the agreed fuel expectation (as stated by the owner).',
                            'Vehicle is allowed only within the approved area/location; taking it elsewhere without owner consent has a ₱5,000 penalty.',
                            'Renter must have a valid (legal) driver’s license and declares no outstanding issues against the license.',
                            'Only the renter (and any approved/authorized driver listed) may drive the vehicle.',
                            'Vehicle may be used only for routine, legal purposes (personal or business) and must follow all Philippine laws and rules.',
                            'Renter is responsible for any damages (dents/scratches) and any cleaning fees incurred during the rental period.',
                            'Renter agrees to hold the owner harmless and confirms the vehicle was inspected and accepted in good operating condition.',
                        ];
                    @endphp

                    <div class="terms-list">
                        @foreach($terms as $t)
                            <div class="term-item">
                                <div class="term-dot">●</div>
                                <div class="term-text">{{ $t }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Booking Card -->
            <div>
                <div class="booking-card">
                    <div class="booking-top">
                        <div>
                            <h3>Book This Car</h3>
                            <p>Fill in the details to continue your booking.</p>
                        </div>

                        <div class="booking-rate">
                            <small>Daily rate</small>
                            <strong>₱{{ number_format($car->price_per_day, 0) }}</strong>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="error-box">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('user.booking.create') }}" method="POST" id="bookingForm">
                        @csrf
                        <input type="hidden" name="car_id" value="{{ $car->id }}">

                        <div class="booking-grid">
                            <div class="form-group-block">
                                <label class="form-label">Pickup Date</label>
                                <input type="text"
                                       id="pickup_date"
                                       name="pickup_date"
                                       value="{{ request('pickup_date') }}"
                                       class="form-input"
                                       required
                                       autocomplete="off">
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">Return Date</label>
                                <input type="text"
                                       id="return_date"
                                       name="return_date"
                                       value="{{ request('return_date') }}"
                                       class="form-input"
                                       required
                                       autocomplete="off">
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">Pickup Time</label>
                                <input type="time"
                                       name="pickup_time"
                                       value="{{ request('pickup_time') }}"
                                       class="form-input"
                                       required>
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">Return Time</label>
                                <input type="time"
                                       name="return_time"
                                       value="{{ request('return_time') }}"
                                       class="form-input"
                                       required>
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">Service Type</label>
                                <select id="service_type_id" name="service_type_id" class="form-select" required>
                                    <option value="" disabled {{ old('service_type_id') ? '' : 'selected' }}>Select service</option>
                                    @foreach($serviceTypes as $st)
                                        <option value="{{ $st->id }}" {{ old('service_type_id') == $st->id ? 'selected' : '' }}>
                                            {{ $st->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group-block">
                                <label class="form-label">Contact Number</label>
                                <input type="tel"
                                       name="phone"
                                       value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                       placeholder="09XXXXXXXXX"
                                       class="form-input"
                                       required>
                                <div class="helper-text">This will be saved to your profile.</div>
                            </div>

                            <div id="locationWrap" class="form-group-block full">
                                <label class="form-label">Pickup / Delivery Location</label>
                                <input id="service_location"
                                       type="text"
                                       name="service_location"
                                       value="{{ old('service_location') }}"
                                       placeholder="Enter address / landmark"
                                       class="form-input">
                                <div class="helper-text">Required for delivery.</div>
                            </div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-row">
                                <span>Daily rate</span>
                                <strong>₱{{ number_format($car->price_per_day, 0) }}</strong>
                            </div>

                            <div class="summary-row">
                                <span>Days</span>
                                <strong id="rental-days">0</strong>
                            </div>

                            <div class="summary-row">
                                <span>Subtotal</span>
                                <strong id="subtotal">₱0.00</strong>
                            </div>

                            <div class="summary-row discount">
                                <span>Points Discount</span>
                                <strong id="pointsValueText">-₱0.00</strong>
                            </div>
                        </div>

                        <div class="total-box">
                            <p>Total</p>
                            <input type="hidden" name="total_price" id="total-price-input" value="0">
                            <h4 id="total-price">₱0.00</h4>
                        </div>

                        @php
                            $availablePoints = (int) (auth()->user()->points_balance ?? 0);
                        @endphp

                        <div class="points-box">
                            <h4>Discount Points</h4>
                            <p>You have <strong>{{ $availablePoints }}</strong> points available.</p>

                            <div class="points-row">
                                <input
                                    type="number"
                                    id="points_to_use"
                                    name="points_to_use"
                                    min="0"
                                    max="{{ $availablePoints }}"
                                    value="{{ old('points_to_use', 0) }}"
                                    class="form-input"
                                    placeholder="0"
                                >

                                <button type="button" id="applyPointsBtn" class="points-btn">
                                    Apply
                                </button>
                            </div>

                            <p id="pointsError" class="inline-message error" style="display:none;"></p>

                            <div style="margin-top:10px;">
                                <p id="pointsValueText" class="inline-message success" style="display:none;"></p>
                                <p id="remainingPointsText" class="inline-message" style="display:none;color:#475569;"></p>
                            </div>

                            <input type="hidden" id="points_discount_amount" name="points_discount_amount" value="0">
                        </div>

                        <div class="agree-wrap">
                            <label class="agree-label">
                                <input type="checkbox" id="agree_terms">
                                <span>
                                    I agree to the <strong>Rental Terms & Conditions</strong>.
                                </span>
                            </label>

                            <p id="agreeError" class="inline-message error" style="display:none;">
                                Please agree to the Rental Terms & Conditions to continue.
                            </p>
                        </div>

                        <button type="submit" id="confirmBtn" disabled class="primary-book-btn">
                            Confirm Booking
                        </button>

                        <p class="booking-note">Free cancellation • No hidden fees</p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    window.BOOKING_CFG = {
      dailyRate: {{ $car->price_per_day }},
      insuranceRate: 0,
      taxRate: 0,
      discountRate: 0,
      unavailableUrl: "{{ route('user.unavailable-dates', $car->id) }}",
      availablePoints: {{ (int) (auth()->user()->points_balance ?? 0) }},
    };
</script>

<script src="{{ asset('js/user/usercardetails.js') }}"></script>
@endsection