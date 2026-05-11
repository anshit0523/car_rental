@extends('layouts.adminlayout')

@php
    $panelPrefix = $panelPrefix ?? (request()->is('manager*') ? 'manager' : 'admin');
@endphp

@section('custom-styles')
<style>
    .calendar-scroll-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }

    .calendar-scroll-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .calendar-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 999px;
    }

    .calendar-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .calendar-table {
        min-width: max-content;
        width: max-content;
    }

    .vehicle-sticky-head,
    .vehicle-sticky-cell {
        position: sticky;
        left: 0;
    }

    .vehicle-sticky-head {
        z-index: 40;
        min-width: 260px;
        max-width: 260px;
        box-shadow: 8px 0 18px rgba(15, 23, 42, 0.12);
    }

    .vehicle-sticky-cell {
        z-index: 20;
        min-width: 260px;
        max-width: 260px;
        box-shadow: 8px 0 18px rgba(15, 23, 42, 0.06);
    }

    @media (max-width: 768px) {
        .calendar-page-wrapper {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .vehicle-sticky-head {
            min-width: 185px;
            max-width: 185px;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .vehicle-sticky-cell {
            min-width: 185px;
            max-width: 185px;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .vehicle-icon-box {
            display: none;
        }

        .vehicle-name {
            font-size: 12px;
            line-height: 1.2;
            white-space: normal;
        }

        .vehicle-plate {
            font-size: 10px;
            padding: 3px 6px;
        }

        .vehicle-meta-badge {
            font-size: 10px;
            padding: 3px 6px;
        }

        .calendar-date-cell {
            min-width: 95px;
        }
    }
</style>
@endsection

@section('content')
<div class="calendar-page-wrapper h-screen overflow-y-auto overflow-x-hidden bg-gradient-to-br from-slate-50 to-slate-100 p-4 md:p-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                        Vehicle Availability
                    </h1>
                    <p class="text-gray-600 mt-2">Manage and view vehicle bookings across all dates</p>
                </div>

                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200">
                    <span class="text-sm font-medium text-gray-700">{{ now()->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="p-4 md:p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <form id="filterForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag text-blue-600 mr-2"></i>Filter by Brand
                            </label>

                            <select
                                name="brand_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="">All Brands</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-blue-600 mr-2"></i>Search
                            </label>

                            <input
                                type="text"
                                name="search"
                                placeholder="Model..."
                                value="{{ request('search') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>Navigation
                            </label>

                            <div class="flex items-center gap-2">
                                @php
                                    $isPastOrToday = \Carbon\Carbon::parse($previousWeek)->startOfDay() < \Carbon\Carbon::today();
                                @endphp

                                @if($isPastOrToday)
                                    <span
                                        class="p-2.5 rounded-lg text-gray-300 cursor-not-allowed select-none"
                                        title="Cannot navigate to past dates">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a
                                        href="{{ route($panelPrefix . '.calendar', ['date' => $previousWeek, 'view' => request('view', 'weekly'), 'brand_id' => request('brand_id'), 'search' => request('search')]) }}"
                                        class="p-2.5 hover:bg-gray-100 rounded-lg transition text-gray-600 hover:text-blue-600">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                <span class="text-sm font-medium text-gray-700 px-3 py-2 bg-gray-50 rounded-lg min-w-fit">
                                    {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
                                </span>

                                <a
                                    href="{{ route($panelPrefix . '.calendar', ['date' => $nextWeek, 'view' => request('view', 'weekly'), 'brand_id' => request('brand_id'), 'search' => request('search')]) }}"
                                    class="p-2.5 hover:bg-gray-100 rounded-lg transition text-gray-600 hover:text-blue-600">
                                    <i class="fas fa-chevron-right"></i>
                                </a>

                                <a
                                    href="{{ route($panelPrefix . '.calendar') }}"
                                    class="ml-auto px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    Today
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-2 border-t border-gray-200">
                        <span class="text-sm font-semibold text-gray-700">View:</span>

                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input
                                    type="radio"
                                    name="view"
                                    value="weekly"
                                    {{ request('view', 'weekly') == 'weekly' ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 cursor-pointer">

                                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">
                                    7 Days
                                </span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input
                                    type="radio"
                                    name="view"
                                    value="30days"
                                    {{ request('view') == '30days' ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 cursor-pointer">

                                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">
                                    30 Days
                                </span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div id="loadingIndicator" class="hidden bg-blue-50 border-b border-blue-200 px-6 py-3 flex items-center gap-2">
                <div class="animate-spin">
                    <i class="fas fa-spinner text-blue-600"></i>
                </div>
                <span class="text-sm font-medium text-blue-600">Updating table...</span>
            </div>

            <div class="calendar-scroll-wrapper">
                <table class="calendar-table">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-800 to-gray-900 text-white sticky top-0 z-20">
                            <th class="vehicle-sticky-head bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 text-left text-sm font-bold">
                                <i class="fas fa-car mr-2"></i>Vehicle Details
                            </th>

                            @foreach($calendarDates as $date)
                                <th class="calendar-date-cell px-3 py-4 text-center min-w-24 whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="font-bold text-base">{{ $date['day'] }}</span>
                                        <span class="text-xs opacity-80">{{ $date['date'] }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($cars as $car)
                            <tr class="hover:bg-blue-50 transition duration-200">
                                <td class="vehicle-sticky-cell bg-white hover:bg-blue-50 px-6 py-3 font-medium">
                                    <div class="flex items-start gap-3">
                                        <div class="vehicle-icon-box p-2.5 bg-blue-100 rounded-lg flex-shrink-0">
                                            <i class="fas fa-car text-blue-600 text-lg"></i>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="vehicle-name font-bold text-gray-900 truncate">
                                                {{ $car->brand->name ?? 'Unknown' }}, {{ $car->model }}
                                            </p>

                                            <div class="mt-1">
                                                @if($car->plate_number)
                                                    <span class="vehicle-plate inline-flex items-center px-2.5 py-1 rounded-md border border-gray-300 bg-white text-xs font-semibold text-gray-700 tracking-wide shadow-sm">
                                                        {{ $car->plate_number }}
                                                    </span>
                                                @else
                                                    <span class="vehicle-plate inline-flex items-center px-2.5 py-1 rounded-md border border-gray-200 bg-gray-50 text-xs font-medium text-gray-400">
                                                        No Plate
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex gap-2 mt-2 flex-wrap">
                                                <span class="vehicle-meta-badge inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                                                    <i class="fas fa-cog"></i>
                                                    {{ $car->transmission->type ?? 'N/A' }}
                                                </span>

                                                <span class="vehicle-meta-badge inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                                                    <i class="fas fa-users"></i>
                                                    {{ $car->seats ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @foreach($calendarDates as $date)
                                    <td class="px-3 py-2 text-center">
                                        @php
                                            $dayStart = \Carbon\Carbon::parse($date['full'])->startOfDay();
                                            $dayEnd = \Carbon\Carbon::parse($date['full'])->endOfDay();

                                            $cellBooking = null;
                                            $bgColor = 'bg-emerald-100';
                                            $textColor = 'text-emerald-700';
                                            $icon = 'fa-check-circle';
                                            $labelTop = 'Available';
                                            $labelBottom = null;
                                            $availableFrom = null;

                                            foreach ($car->bookings->sortBy('pickup_at') as $b) {
                                                $status = strtolower($b->status->name ?? '');
                                                $bookingStart = \Carbon\Carbon::parse($b->pickup_at);
                                                $bookingEnd = \Carbon\Carbon::parse($b->return_at);

                                                $isBlocking = in_array($status, [
                                                    'confirmed',
                                                    'reserved',
                                                    'pending',
                                                    'approved',
                                                    'active',
                                                    'pending payment verification'
                                                ]);

                                                $overlapsDay = $bookingStart->lt($dayEnd) && $bookingEnd->gt($dayStart);

                                                if ($isBlocking && $overlapsDay) {
                                                    $cellBooking = $b;
                                                    $isBookedStyle = in_array($status, ['confirmed', 'approved', 'active']);

                                                    if ($bookingEnd->isSameDay($dayStart) && $bookingEnd->lt($dayEnd)) {
                                                        if ($isBookedStyle) {
                                                            $bgColor = 'bg-red-500';
                                                            $textColor = 'text-white';
                                                            $icon = 'fa-lock';
                                                            $labelTop = 'Booked';
                                                            $labelBottom = 'Avail ' . $bookingEnd->format('g:i A');
                                                        } else {
                                                            $bgColor = 'bg-amber-500';
                                                            $textColor = 'text-white';
                                                            $icon = 'fa-hourglass-half';
                                                            $labelTop = 'Reserved';
                                                            $labelBottom = 'Avail ' . $bookingEnd->format('g:i A');
                                                        }

                                                        $availableFrom = $bookingEnd->format('g:i A');
                                                    } elseif ($bookingStart->isSameDay($dayStart) && $bookingStart->gt($dayStart)) {
                                                        $bgColor = 'bg-sky-100';
                                                        $textColor = 'text-sky-700';
                                                        $icon = 'fa-clock';
                                                        $labelTop = 'Available';
                                                        $labelBottom = 'Until ' . $bookingStart->format('g:i A');
                                                    } else {
                                                        if ($isBookedStyle) {
                                                            $bgColor = 'bg-red-500';
                                                            $textColor = 'text-white';
                                                            $icon = 'fa-lock';
                                                            $labelTop = 'Booked';
                                                            $labelBottom = null;
                                                        } else {
                                                            $bgColor = 'bg-amber-500';
                                                            $textColor = 'text-white';
                                                            $icon = 'fa-hourglass-half';
                                                            $labelTop = 'Reserved';
                                                            $labelBottom = null;
                                                        }
                                                    }

                                                    break;
                                                }
                                            }
                                        @endphp

                                        @if($cellBooking)
                                            <button
                                                onclick="window.openBookingModal({{ $cellBooking->id }})"
                                                class="inline-flex items-center gap-2 px-3 py-2 {{ $bgColor }} {{ $textColor }} rounded-lg text-xs font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105 cursor-pointer"
                                                @if($availableFrom) title="Available again at {{ $availableFrom }}" @endif>
                                                <i class="fas {{ $icon }} text-xs self-start mt-0.5"></i>

                                                <span class="flex flex-col leading-tight text-left">
                                                    <span>{{ $labelTop }}</span>

                                                    @if($labelBottom)
                                                        <span class="text-[10px] opacity-90">{{ $labelBottom }}</span>
                                                    @endif
                                                </span>
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                onclick="window.openCreateBookingModal(
                                                    {{ $car->id }},
                                                    '{{ \Carbon\Carbon::parse($date['full'])->format('Y-m-d') }}',
                                                    @js(($car->brand->name ?? 'Unknown') . ' ' . $car->model),
                                                    '09:00',
                                                    '{{ \Carbon\Carbon::parse($date['full'])->format('Y-m-d') }}',
                                                    '18:00'
                                                )"
                                                class="inline-flex items-center gap-2 px-3 py-2 {{ $bgColor }} {{ $textColor }} rounded-lg text-xs font-semibold hover:opacity-90 transition">
                                                <i class="fas {{ $icon }} self-start mt-0.5"></i>

                                                <span class="flex flex-col leading-tight text-left">
                                                    <span>{{ $labelTop }}</span>

                                                    @if($labelBottom)
                                                        <span class="text-[10px] opacity-90">{{ $labelBottom }}</span>
                                                    @endif
                                                </span>
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($calendarDates) + 1 }}" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-inbox text-4xl text-gray-300"></i>
                                        <p class="text-gray-500 font-medium">No vehicles found</p>
                                        <p class="text-gray-400 text-sm">Try adjusting your search filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-medium text-gray-600">Available</span>
                </div>

                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-sky-500"></div>
                    <span class="text-xs font-medium text-gray-600">Available Until Time</span>
                </div>

                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <span class="text-xs font-medium text-gray-600">Reserved</span>
                </div>

                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <span class="text-xs font-medium text-gray-600">Booked</span>
                </div>
            </div>
        </div>
    </div>

    <div id="bookingModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg animate-fade-in">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-bold text-gray-800">Booking Details</h2>

                    <button onclick="window.closeBookingModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div id="bookingModalContent" class="px-6 py-4 space-y-4">
                    <div class="text-center text-gray-400">Loading booking details...</div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button
                        id="openAddBookingFromDetailsBtn"
                        type="button"
                        onclick="window.openCreateFromBookingModal()"
                        class="hidden px-4 py-2 text-sm rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Add Booking
                    </button>

                    <button
                        onclick="window.closeBookingModal()"
                        class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="createBookingModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <div class="relative w-full h-full overflow-y-auto">
            <div class="min-h-full flex items-start lg:items-center justify-end p-3 md:p-5 lg:pr-8">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md sm:max-w-2xl lg:max-w-4xl max-h-[90vh] overflow-hidden my-4 flex flex-col">
                    <div class="flex items-center justify-between px-5 md:px-6 py-4 border-b bg-white">
                        <h2 class="text-lg md:text-xl font-bold text-gray-800">Create Walk-in Booking</h2>

                        <button type="button" onclick="window.closeCreateBookingModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <form id="createBookingForm" class="lg:grid lg:grid-cols-[260px_1fr]">
                            @csrf

                            <input type="hidden" name="car_id" id="modal_car_id">
                            <input type="hidden" name="existing_user_id" id="existing_user_id">

                            <div class="bg-slate-50 border-b lg:border-b-0 lg:border-r border-gray-200 px-5 py-5 md:px-6 md:py-6">
                                <div class="space-y-5">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 mb-2">
                                            Booking Summary
                                        </p>

                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Selected Car</label>

                                        <input
                                            type="text"
                                            id="modal_car_name"
                                            readonly
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-sm">
                                    </div>

                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pickup Date</label>
                                            <input
                                                type="date"
                                                name="pickup_date"
                                                id="modal_pickup_date"
                                                min="{{ now()->format('Y-m-d') }}"
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pickup Time</label>
                                            <input
                                                type="time"
                                                name="pickup_time"
                                                value="09:00"
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Return Date</label>
                                            <input
                                                type="date"
                                                name="return_date"
                                                id="modal_return_date"
                                                min="{{ now()->format('Y-m-d') }}"
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Return Time</label>
                                            <input
                                                type="time"
                                                name="return_time"
                                                value="18:00"
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Service Type</label>

                                            <select
                                                name="service_type_id"
                                                id="service_type_id"
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                                <option value="">Select service type</option>

                                                @foreach($serviceTypes as $serviceType)
                                                    <option value="{{ $serviceType->id }}" data-name="{{ strtolower($serviceType->name) }}">
                                                        {{ $serviceType->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="serviceLocationWrapper" class="hidden">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Service Location</label>

                                            <input
                                                type="text"
                                                name="service_location"
                                                id="service_location"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                                placeholder="Required only for delivery">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>

                                            <select
                                                name="payment_method_code"
                                                required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                                @foreach($paymentMethods as $paymentMethod)
                                                    <option value="{{ $paymentMethod->code }}" {{ $paymentMethod->code === 'cash' ? 'selected' : '' }}>
                                                        {{ $paymentMethod->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5 py-5 md:px-6 md:py-6">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Customer Type</label>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <label class="border border-gray-300 rounded-xl px-4 py-3 cursor-pointer hover:border-blue-500 transition">
                                                <div class="flex items-start gap-3">
                                                    <input
                                                        type="radio"
                                                        name="customer_type"
                                                        value="existing"
                                                        class="mt-1"
                                                        onchange="window.toggleCustomerType()">

                                                    <div>
                                                        <p class="font-semibold text-gray-800">Existing Customer</p>
                                                        <p class="text-sm text-gray-500">Reuse an existing account</p>
                                                    </div>
                                                </div>
                                            </label>

                                            <label class="border border-gray-300 rounded-xl px-4 py-3 cursor-pointer hover:border-blue-500 transition">
                                                <div class="flex items-start gap-3">
                                                    <input
                                                        type="radio"
                                                        name="customer_type"
                                                        value="new"
                                                        class="mt-1"
                                                        checked
                                                        onchange="window.toggleCustomerType()">

                                                    <div>
                                                        <p class="font-semibold text-gray-800">New Walk-in Customer</p>
                                                        <p class="text-sm text-gray-500">Create a new customer account</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div id="existingCustomerSection" class="hidden space-y-4 border border-blue-100 bg-blue-50/50 rounded-2xl p-4">
                                        <div>
                                            <h3 class="font-semibold text-gray-800">Find Existing Customer</h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Search by name, email, or phone, then reuse the customer account.
                                            </p>
                                        </div>

                                        <div class="relative">
                                            <input
                                                type="text"
                                                name="existing_customer_search"
                                                id="existing_customer_search"
                                                placeholder="Type name, email, or phone"
                                                autocomplete="off"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">

                                            <div
                                                id="existingCustomerDropdown"
                                                class="hidden absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                                            </div>
                                        </div>

                                        <div id="existingCustomerResult" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                            <p class="text-sm font-semibold text-emerald-700 mb-3">Selected Customer</p>

                                            <div class="flex flex-col gap-2 text-sm">
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="font-semibold text-gray-700">Name:</span>
                                                    <span id="existing_customer_name" class="text-gray-900"></span>
                                                </div>

                                                <div class="flex flex-wrap gap-2">
                                                    <span class="font-semibold text-gray-700">Phone:</span>
                                                    <span id="existing_customer_phone" class="text-gray-900"></span>
                                                </div>

                                                <div class="flex flex-wrap gap-2">
                                                    <span class="font-semibold text-gray-700">Email:</span>
                                                    <span id="existing_customer_email" class="text-gray-900"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="existingCustomerNotFound" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                                            Customer not found. Use <strong>New Walk-in Customer</strong> instead.
                                        </div>
                                    </div>

                                    <div id="newCustomerSection" class="space-y-4 border border-gray-200 rounded-2xl p-4">
                                        <div>
                                            <h3 class="font-semibold text-gray-800">New Walk-in Customer</h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Create a new customer account and set a password.
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Name</label>
                                                <input
                                                    type="text"
                                                    name="customer_name"
                                                    id="new_customer_name"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                                                <input
                                                    type="text"
                                                    name="customer_phone"
                                                    id="new_customer_phone"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                                <input
                                                    type="email"
                                                    name="customer_email"
                                                    id="new_customer_email"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                                    placeholder="Enter email">
                                                <p class="mt-1 text-xs text-gray-500">Email must be unique.</p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>

                                                <div class="relative">
                                                    <input
                                                        type="password"
                                                        name="password"
                                                        id="new_customer_password"
                                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl text-sm"
                                                        placeholder="Minimum 8 characters">

                                                    <button
                                                        type="button"
                                                        onclick="window.togglePassword('new_customer_password', this)"
                                                        class="absolute inset-y-0 right-0 px-4 text-gray-500 hover:text-gray-700">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>

                                                <div class="relative">
                                                    <input
                                                        type="password"
                                                        name="password_confirmation"
                                                        id="new_customer_password_confirmation"
                                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl text-sm"
                                                        placeholder="Re-enter password">

                                                    <button
                                                        type="button"
                                                        onclick="window.togglePassword('new_customer_password_confirmation', this)"
                                                        class="absolute inset-y-0 right-0 px-4 text-gray-500 hover:text-gray-700">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="liveAvailabilityBox" class="hidden rounded-xl px-4 py-3 text-sm border"></div>
                                    <div id="createBookingError" class="hidden rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600"></div>
                                    <div id="createBookingSuccess" class="hidden rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-600"></div>

                                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-3 border-t">
                                        <button
                                            type="button"
                                            onclick="window.closeCreateBookingModal()"
                                            class="px-5 py-3 text-sm rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200">
                                            Close
                                        </button>

                                        <button
                                            type="submit"
                                            id="saveBookingBtn"
                                            class="px-6 py-3 text-sm rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                                            Save Booking
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.AdminCalendarConfig = {
        calendarUrl: @json(route($panelPrefix . '.calendar')),
        bookingStoreUrl: @json(route($panelPrefix . '.bookings.store')),
        bookingDetailsBaseUrl: @json(url($panelPrefix . '/bookings')),
        checkAvailabilityUrl: @json(route($panelPrefix . '.bookings.check-availability-exact')),
        searchCustomerUrl: @json(route($panelPrefix . '.bookings.search-customer')),
    };
</script>

<script src="{{ asset('js/admin/admincalendar.js') }}?v={{ time() }}"></script>
@endsection