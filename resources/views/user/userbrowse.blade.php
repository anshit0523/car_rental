@extends('layouts.userlayout')

@section('custom-styles')
<style>
    .car-listing-section {
        padding-top: 30px;
    }

    .car-sidebar .sidebar-widget {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
    }

    .car-sidebar .widget-title h4 {
        font-size: 22px;
        line-height: 30px;
        margin-bottom: 18px;
    }

    .car-sidebar .form-control {
        height: 54px;
        border-radius: 10px;
        font-size: 16px;
        border: 1px solid #dcdcdc;
        box-shadow: none;
    }

    .car-sidebar label {
        font-size: 15px;
        font-weight: 600;
        color: #111;
        margin-bottom: 8px;
        display: block;
    }

    .car-sidebar input[type="range"] {
        accent-color: #7a3b26;
    }

    .item-shorting {
        margin-bottom: 35px;
    }

    .item-shorting .left-column h3 {
        font-size: 34px;
        line-height: 42px;
        margin-bottom: 8px;
    }

    .item-shorting .left-column p {
        font-size: 17px;
        color: #555;
    }

    .item-shorting .form-select {
        min-width: 250px;
        height: 50px;
        border-radius: 10px;
        border: 1px solid #dcdcdc;
        box-shadow: none;
    }

    .empty-state-box {
        background: #fff;
        border-radius: 18px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,.04);
        max-width: 760px;
        margin: 40px auto 0;
    }

    .empty-state-box h4 {
        font-size: 28px;
        line-height: 38px;
        margin-bottom: 12px;
    }

    .empty-state-box p {
        font-size: 17px;
        color: #666;
    }

    .filter-check-list li {
        margin-bottom: 14px;
    }

    .filter-check-list label {
        margin-bottom: 0;
        font-size: 16px;
        font-weight: 500;
        color: #444;
    }

    /* 2 cards per row showcase style */
    .car-two-grid-card {
        position: relative;
        height: 100%;
        margin-bottom: 30px;
    }

    .car-two-grid-image-wrap {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
    }

    .car-two-grid-image {
        width: 100%;
        height: 320px;
        object-fit: cover;
        display: block;
    }

    .car-two-grid-price {
        position: absolute;
        top: 24px;
        right: 24px;
        background: #f8f8f8;
        border-radius: 18px;
        padding: 16px 26px;
        font-size: 20px;
        font-weight: 500;
        color: #334155;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
    }

    .car-two-grid-price span {
        color: #ff5a1f;
        font-size: 24px;
        font-weight: 800;
        margin-right: 6px;
    }

    .car-two-grid-content {
        position: relative;
        width: calc(100% - 48px);
        margin: -58px auto 0;
        background: #f8f8f8;
        border-radius: 18px;
        padding: 28px 34px 36px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.08);
        z-index: 2;
        min-height: 300px;
    }

    .car-two-grid-title {
        font-size: 26px;
        line-height: 1.2;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 28px;
    }

    .car-two-grid-title a {
        text-decoration: none;
        color: #0f172a;
    }

    .car-two-grid-info {
        list-style: none;
        padding: 0;
        margin: 0 0 30px 0;
    }

    .car-two-grid-info li {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 18px;
        color: #0f172a;
        margin-bottom: 18px;
    }

    .car-two-grid-info li i {
        color: #ff5a1f;
        font-size: 20px;
        width: 22px;
        text-align: center;
    }

    .car-two-grid-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 68px;
        border: 2px solid #374151;
        border-radius: 18px;
        background: transparent;
        color: #0f172a;
        font-size: 20px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .car-two-grid-btn:hover {
        background: #ff5a1f;
        border-color: #ff5a1f;
        color: #fff;
    }

    @media (max-width: 991px) {
        .car-listing-section {
            padding-top: 20px;
        }

        .item-shorting .left-column h3 {
            font-size: 28px;
            line-height: 36px;
        }

        .item-shorting .form-select {
            min-width: 100%;
        }

        .car-two-grid-image {
            height: 260px;
        }

        .car-two-grid-content {
            width: calc(100% - 24px);
            margin-top: -40px;
            padding: 22px 22px 26px;
            min-height: auto;
        }

        .car-two-grid-title {
            font-size: 22px;
        }

        .car-two-grid-info li {
            font-size: 16px;
        }

        .car-two-grid-btn {
            height: 58px;
            font-size: 18px;
        }
    }

    .pagination-wrapper svg {
    max-width: 16px !important;
    max-height: 16px !important;
    width: 16px !important;
    height: 16px !important;
}
</style>
@endsection

@section('content')
@php
    $hasFilters =
        request()->filled('location') ||
        request()->filled('min_price') ||
        request()->filled('max_price') ||
        request()->filled('brand_id') ||
        request()->filled('fuel_type_id') ||
        request()->filled('transmission_id') ||
        request()->filled('min_seats');
@endphp

<section class="car-listing-section pb_100">
    <div class="container">
        <div class="row clearfix">

            <!-- Filter Sidebar -->
            <div class="col-lg-3 col-md-12 col-sm-12 sidebar-side">
                <div class="car-sidebar">
                    <div class="sidebar-widget search-widget">
                        <div class="widget-title">
                            <h4>Filter Cars</h4>
                        </div>

                        <div class="widget-content">
                            <form action="{{ route('user.search') }}" method="GET" id="filterForm">
                                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'price_low') }}">

                                <div class="mb_25">
                                    <label>Pick-up Date</label>
                                    <input
                                        type="date"
                                        name="pickup_date"
                                        min="{{ now()->format('Y-m-d') }}"
                                        value="{{ request('pickup_date') }}"
                                        class="form-control"
                                    >
                                </div>

                                <div class="mb_25">
                                    <label>Return Date</label>
                                    <input
                                        type="date"
                                        name="return_date"
                                        min="{{ now()->format('Y-m-d') }}"
                                        value="{{ request('return_date') }}"
                                        class="form-control"
                                    >
                                </div>

                                <div class="mb_25">
                                    <label>Time</label>
                                    <input
                                        type="time"
                                        name="time"
                                        value="{{ request('time') }}"
                                        class="form-control"
                                    >
                                </div>

                                <div class="mb_20">
                                    <button type="submit" class="theme-btn btn-style-one w-100" style="border-radius:10px;">
                                        <span>Search Cars</span>
                                    </button>
                                </div>

                                <div class="mt_30">
                                    <div class="widget-title">
                                        <h4>Price Range</h4>
                                    </div>
                                    <input
                                        type="range"
                                        name="max_price"
                                        min="0"
                                        max="8000"
                                        value="{{ request('max_price', 8000) }}"
                                        class="w-100"
                                        onchange="document.getElementById('filterForm').submit()"
                                    >
                                    <div class="d-flex justify-content-between mt_10">
                                        <span>₱0</span>
                                        <span>₱{{ request('max_price', 8000) }}</span>
                                    </div>
                                </div>

                                <div class="mt_30">
                                    <div class="widget-title">
                                        <h4>Brand</h4>
                                    </div>
                                    <ul class="filter-check-list clearfix">
                                        @foreach($brands as $brand)
                                            <li>
                                                <label class="d-flex align-items-center" style="gap:10px; cursor:pointer;">
                                                    <input
                                                        type="checkbox"
                                                        name="brand_id[]"
                                                        value="{{ $brand->id }}"
                                                        @checked(in_array($brand->id, (array) request('brand_id', [])))
                                                        onchange="document.getElementById('filterForm').submit()"
                                                    >
                                                    <span>{{ $brand->name }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="mt_30">
                                    <div class="widget-title">
                                        <h4>Fuel Type</h4>
                                    </div>
                                    <ul class="filter-check-list clearfix">
                                        @foreach($fuelTypes as $fuelType)
                                            <li>
                                                <label class="d-flex align-items-center" style="gap:10px; cursor:pointer;">
                                                    <input
                                                        type="checkbox"
                                                        name="fuel_type_id[]"
                                                        value="{{ $fuelType->id }}"
                                                        @checked(in_array($fuelType->id, (array) request('fuel_type_id', [])))
                                                        onchange="document.getElementById('filterForm').submit()"
                                                    >
                                                    <span>{{ $fuelType->type }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="mt_30">
                                    <div class="widget-title">
                                        <h4>Transmission</h4>
                                    </div>
                                    <ul class="filter-check-list clearfix">
                                        @foreach($transmissions as $transmission)
                                            <li>
                                                <label class="d-flex align-items-center" style="gap:10px; cursor:pointer;">
                                                    <input
                                                        type="checkbox"
                                                        name="transmission_id[]"
                                                        value="{{ $transmission->id }}"
                                                        @checked(in_array($transmission->id, (array) request('transmission_id', [])))
                                                        onchange="document.getElementById('filterForm').submit()"
                                                    >
                                                    <span>{{ $transmission->type }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="mt_30">
                                    <a href="{{ route('user.browse') }}" class="theme-btn btn-style-three w-100 d-block text-center" style="border-radius:10px;">
                                        <span>Clear All Filters</span>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Side -->
            <div class="col-lg-9 col-md-12 col-sm-12 content-side">
                <div class="car-list-content">

                    <div class="item-shorting d-flex justify-content-between align-items-center flex-wrap" style="gap:15px;">
                        <div class="left-column">
                            <h3>Available Cars</h3>
                            <p>{{ $cars->total() }} cars found</p>
                        </div>

                        <div class="right-column">
                            <form action="{{ route(request()->filled('pickup_date') && request()->filled('return_date') ? 'user.search' : 'user.browse') }}"
                                  method="GET">
                                <input type="hidden" name="pickup_date" value="{{ request('pickup_date') }}">
                                <input type="hidden" name="return_date" value="{{ request('return_date') }}">
                                <input type="hidden" name="time" value="{{ request('time') }}">

                                @foreach((array) request('brand_id', []) as $brandId)
                                    <input type="hidden" name="brand_id[]" value="{{ $brandId }}">
                                @endforeach

                                @foreach((array) request('fuel_type_id', []) as $fuelTypeId)
                                    <input type="hidden" name="fuel_type_id[]" value="{{ $fuelTypeId }}">
                                @endforeach

                                @foreach((array) request('transmission_id', []) as $transmissionId)
                                    <input type="hidden" name="transmission_id[]" value="{{ $transmissionId }}">
                                @endforeach

                                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                                <input type="hidden" name="min_seats" value="{{ request('min_seats') }}">

                                <select name="sort_by" onchange="this.form.submit()" class="form-select">
                                    <option value="price_low" @selected(request('sort_by', 'price_low') == 'price_low')>
                                        Price (Low to High)
                                    </option>
                                    <option value="price_high" @selected(request('sort_by') == 'price_high')>
                                        Price (High to Low)
                                    </option>
                                    <option value="newest" @selected(request('sort_by') == 'newest')>
                                        Newest
                                    </option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="row clearfix">
                        @forelse($cars as $car)
                            <div class="col-lg-6 col-md-6 col-sm-12 car-block">
                                <div class="car-two-grid-card">
                                    <div class="car-two-grid-image-wrap">
                                        @if($car->images && count(json_decode($car->images)) > 0)
                                            @php
                                                $images = json_decode($car->images);
                                                $firstImage = $images[0];
                                            @endphp
                                            <img
                                                src="{{ asset('storage/' . $firstImage) }}"
                                                alt="{{ $car->model }}"
                                                class="car-two-grid-image"
                                                onerror="this.onerror=null;this.src='{{ asset('images/no-car-image.png') }}';"
                                            >
                                        @else
                                            <div class="car-two-grid-image d-flex align-items-center justify-content-center" style="background:#e5e7eb; color:#64748b;">
                                                No Image
                                            </div>
                                        @endif

                                        <div class="car-two-grid-price">
                                            <span>₱{{ number_format($car->price_per_day, 0) }}</span> / Day
                                        </div>
                                    </div>

                                    <div class="car-two-grid-content">
                                        <h3 class="car-two-grid-title">
                                            <a href="{{ route('user.car-detail', [
                                                'id' => $car->id,
                                                'pickup_date' => request('pickup_date'),
                                                'return_date' => request('return_date'),
                                                'pickup_time' => request('time'),
                                                'return_time' => request('time')
                                            ]) }}">
                                                {{ $car->brand->name ?? 'N/A' }} {{ $car->model }}
                                            </a>
                                        </h3>

                                        <ul class="car-two-grid-info">
                                            <li>
                                                <i class="fas fa-car-side"></i>
                                                <span>Doors: {{ $car->doors ?? '2' }}</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-cogs"></i>
                                                <span>Transmission: {{ $car->transmission?->type ?? 'Manual' }}</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-user"></i>
                                                <span>Passengers: {{ $car->seats ?? '0' }}</span>
                                            </li>
                                        </ul>

                                        <a href="{{ route('user.car-detail', [
                                            'id' => $car->id,
                                            'pickup_date' => request('pickup_date'),
                                            'return_date' => request('return_date'),
                                            'pickup_time' => request('time'),
                                            'return_time' => request('time')
                                        ]) }}" class="car-two-grid-btn">
                                            Rent Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="empty-state-box">
                                    @if(!request()->filled('pickup_date') && !$hasFilters)
                                        <h4>Search or apply filters to find available cars</h4>
                                        <p>Select pickup and return dates or choose your preferred filters.</p>
                                    @else
                                        <h4>No cars available matching your criteria</h4>
                                        <p>Try changing the dates or clearing some filters.</p>
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="pagination-wrapper centred pt_10">
                        {{ $cars->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@section('scripts')
    <script src="{{ asset('js/user/userbrowsecar.js') }}"></script>
@endsection