@extends('layouts.adminlayout')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-4 md:p-8">
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
                            <select name="brand_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
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
                            <div class="relative">
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Model..."
                                    value="{{ request('search') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                >
                            </div>
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
                                    <span class="p-2.5 rounded-lg text-gray-300 cursor-not-allowed select-none" title="Cannot navigate to past dates">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                @else
                                    <a
                                        href="{{ route('admin.calendar', ['date' => $previousWeek, 'view' => request('view', 'weekly'), 'brand_id' => request('brand_id'), 'search' => request('search')]) }}"
                                        class="p-2.5 hover:bg-gray-100 rounded-lg transition text-gray-600 hover:text-blue-600"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                @endif

                                <span class="text-sm font-medium text-gray-700 px-3 py-2 bg-gray-50 rounded-lg min-w-fit">
                                    {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
                                </span>

                                <a
                                    href="{{ route('admin.calendar', ['date' => $nextWeek, 'view' => request('view', 'weekly'), 'brand_id' => request('brand_id'), 'search' => request('search')]) }}"
                                    class="p-2.5 hover:bg-gray-100 rounded-lg transition text-gray-600 hover:text-blue-600"
                                >
                                    <i class="fas fa-chevron-right"></i>
                                </a>

                                <a href="{{ route('admin.calendar') }}" class="ml-auto px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    Today
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-2 border-t border-gray-200">
                        <span class="text-sm font-semibold text-gray-700">View:</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="view" value="weekly" {{ request('view', 'weekly') == 'weekly' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">7 Days</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="view" value="30days" {{ request('view') == '30days' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">30 Days</span>
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

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-800 to-gray-900 text-white sticky top-0 z-20">
                            <th class="sticky left-0 bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4 text-left text-sm font-bold z-30 min-w-64">
                                <i class="fas fa-car mr-2"></i>Vehicle Details
                            </th>
                            @foreach($calendarDates as $date)
                                <th class="px-3 py-4 text-center min-w-24 whitespace-nowrap">
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
                                <td class="sticky left-0 bg-white hover:bg-blue-50 px-6 py-3 font-medium z-10">
                                    <div class="flex items-start gap-3">
                                        <div class="p-2.5 bg-blue-100 rounded-lg flex-shrink-0">
                                            <i class="fas fa-car text-blue-600 text-lg"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-900 truncate">{{ $car->brand->name ?? 'Unknown' }}, {{ $car->model }}</p>
                                            <div class="flex gap-2 mt-2 flex-wrap">
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                                                    <i class="fas fa-cog"></i>{{ $car->transmission->type ?? 'N/A' }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                                                    <i class="fas fa-users"></i>{{ $car->seats }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @foreach($calendarDates as $date)
                                    <td class="px-3 py-2 text-center">
                                        @php
                                            $currentDate = \Carbon\Carbon::parse($date['full'])->format('Y-m-d');
                                            $cellBooking = null;
                                            $bgColor = 'bg-emerald-100';
                                            $icon = 'fa-check-circle';
                                            $label = 'Available';

                                            foreach ($car->bookings as $b) {
                                                $status = strtolower($b->status->name ?? '');
                                                $pickupDate = \Carbon\Carbon::parse($b->pickup_at)->format('Y-m-d');
                                                $returnDate = \Carbon\Carbon::parse($b->return_at)->format('Y-m-d');

                                                if (
                                                    in_array($status, ['confirmed', 'reserved', 'pending', 'approved']) &&
                                                    $pickupDate <= $currentDate &&
                                                    $returnDate >= $currentDate
                                                ) {
                                                    $cellBooking = $b;

                                                    if (in_array($status, ['confirmed', 'approved'])) {
                                                        $bgColor = 'bg-red-500';
                                                        $icon = 'fa-lock';
                                                        $label = 'Booked';
                                                    } else {
                                                        $bgColor = 'bg-amber-500';
                                                        $icon = 'fa-hourglass-half';
                                                        $label = 'Reserved';
                                                    }
                                                    break;
                                                }
                                            }
                                        @endphp

                                        @if($cellBooking && in_array(strtolower($cellBooking->status->name ?? ''), ['confirmed', 'reserved', 'pending', 'approved']))
                                            <button
                                                onclick="openBookingModal({{ $cellBooking->id }})"
                                                class="inline-flex items-center gap-1 px-3 py-2 {{ $bgColor }} text-white rounded-lg text-xs font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105 cursor-pointer"
                                            >
                                                <i class="fas {{ $icon }} text-xs"></i>
                                                <span>{{ $label }}</span>
                                            </button>
                                        @else
                                            <button
                                                type="button"
                                                onclick="openCreateBookingModal(
                                                    {{ $car->id }},
                                                    '{{ \Carbon\Carbon::parse($date['full'])->format('Y-m-d') }}',
                                                    @js(($car->brand->name ?? 'Unknown') . ' ' . $car->model)
                                                )"
                                                class="inline-flex items-center gap-1 px-3 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold hover:bg-emerald-200 transition"
                                            >
                                                <i class="fas fa-plus-circle"></i>
                                                <span>Available</span>
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
</div>
@endsection

<div id="bookingModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg animate-fade-in">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-lg font-bold text-gray-800">Booking Details</h2>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div id="bookingModalContent" class="px-6 py-4 space-y-4">
                <div class="text-center text-gray-400">Loading booking details...</div>
            </div>

            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button onclick="closeBookingModal()" class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div id="createBookingModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden">
    <div class="min-h-screen flex items-center justify-center p-3 md:p-6">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md sm:max-w-2xl lg:max-w-5xl overflow-hidden">
            <div class="flex items-center justify-between px-5 md:px-6 py-4 border-b bg-white">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Create Walk-in Booking</h2>
                <button type="button" onclick="closeCreateBookingModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="createBookingForm" class="lg:grid lg:grid-cols-[320px_1fr] lg:min-h-[620px]">
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-sm"
                            >
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pickup Date</label>
                                <input
                                    type="date"
                                    name="pickup_date"
                                    id="modal_pickup_date"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pickup Time</label>
                                <input
                                    type="time"
                                    name="pickup_time"
                                    value="09:00"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Return Date</label>
                                <input
                                    type="date"
                                    name="return_date"
                                    id="modal_return_date"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Return Time</label>
                                <input
                                    type="time"
                                    name="return_time"
                                    value="18:00"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Service Type</label>
                                <select name="service_type_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                    <option value="">Select service type</option>
                                    @foreach($serviceTypes as $serviceType)
                                        <option value="{{ $serviceType->id }}">{{ $serviceType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                <select name="status_name" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm">
                                    <option value="Confirmed" selected>Confirmed</option>
                                    <option value="Pending">Pending</option>
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
                                            onchange="toggleCustomerType()"
                                        >
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
                                            onchange="toggleCustomerType()"
                                        >
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
                                    Search by email or phone, then reuse the customer account.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3">
                                <input
                                    type="text"
                                    name="existing_customer_search"
                                    id="existing_customer_search"
                                    placeholder="Enter email or phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                >
                                <button
                                    type="button"
                                    id="searchExistingCustomerBtn"
                                    class="px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700"
                                >
                                    Search
                                </button>
                            </div>

                            <div id="existingCustomerResult" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                <p class="text-sm font-semibold text-emerald-700 mb-3">Customer Found</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Name</label>
                                        <input
                                            type="text"
                                            id="existing_customer_name"
                                            readonly
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-sm"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Phone</label>
                                        <input
                                            type="text"
                                            id="existing_customer_phone"
                                            readonly
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-sm"
                                        >
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                                        <input
                                            type="text"
                                            id="existing_customer_email"
                                            readonly
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-white text-sm"
                                        >
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
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                                    <input
                                        type="text"
                                        name="customer_phone"
                                        id="new_customer_phone"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                    <input
                                        type="email"
                                        name="customer_email"
                                        id="new_customer_email"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                        placeholder="Enter email"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                                    <input
                                        type="password"
                                        name="password"
                                        id="new_customer_password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        id="new_customer_password_confirmation"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Service Location</label>
                            <input
                                type="text"
                                name="service_location"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm"
                                placeholder="Required only for delivery"
                            >
                        </div>

                        <div id="createBookingError" class="hidden rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600"></div>
                        <div id="createBookingSuccess" class="hidden rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-600"></div>

                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-3 border-t">
                            <button
                                type="button"
                                onclick="closeCreateBookingModal()"
                                class="px-5 py-3 text-sm rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200"
                            >
                                Close
                            </button>
                            <button
                                type="submit"
                                class="px-6 py-3 text-sm rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700"
                            >
                                Save Booking
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showLoading() {
    document.getElementById('loadingIndicator')?.classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingIndicator')?.classList.add('hidden');
}

function buildFilterUrl() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const newParams = new URLSearchParams(formData);
    const current = new URLSearchParams(window.location.search);

    current.forEach((value, key) => {
        if (!newParams.has(key)) {
            newParams.set(key, value);
        }
    });

    return `{{ route('admin.calendar') }}?${newParams.toString()}`;
}

function fetchFilteredData() {
    showLoading();

    const url = buildFilterUrl();
    history.replaceState(null, '', url);

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.text();
    })
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newWrapper = doc.querySelector('.overflow-x-auto');
        const curWrapper = document.querySelector('.overflow-x-auto');
        if (newWrapper && curWrapper) {
            curWrapper.innerHTML = newWrapper.innerHTML;
        }

        const newNav = doc.querySelector('.min-w-fit');
        const curNav = document.querySelector('.min-w-fit');
        if (newNav && curNav) {
            curNav.textContent = newNav.textContent;
        }

        hideLoading();
    })
    .catch(err => {
        console.error('Filter fetch failed:', err);
        hideLoading();
    });
}

function toggleCustomerType() {
    const selected = document.querySelector('input[name="customer_type"]:checked')?.value;
    const existingSection = document.getElementById('existingCustomerSection');
    const newSection = document.getElementById('newCustomerSection');

    const newName = document.getElementById('new_customer_name');
    const newPhone = document.getElementById('new_customer_phone');
    const newEmail = document.getElementById('new_customer_email');
    const newPassword = document.getElementById('new_customer_password');
    const newPasswordConfirmation = document.getElementById('new_customer_password_confirmation');

    if (selected === 'existing') {
        existingSection.classList.remove('hidden');
        newSection.classList.add('hidden');

        newName.removeAttribute('required');
        newPhone.removeAttribute('required');
        newEmail.removeAttribute('required');
        newPassword.removeAttribute('required');
        newPasswordConfirmation.removeAttribute('required');
    } else {
        existingSection.classList.add('hidden');
        newSection.classList.remove('hidden');

        newName.setAttribute('required', 'required');
        newPhone.setAttribute('required', 'required');
        newEmail.setAttribute('required', 'required');
        newPassword.setAttribute('required', 'required');
        newPasswordConfirmation.setAttribute('required', 'required');

        document.getElementById('existing_user_id').value = '';
        document.getElementById('existingCustomerResult').classList.add('hidden');
        document.getElementById('existingCustomerNotFound').classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filterForm');
    const searchInput = form?.querySelector('input[name="search"]');
    const brandSelect = form?.querySelector('select[name="brand_id"]');
    const modal = document.getElementById('bookingModal');
    const createModal = document.getElementById('createBookingModal');

    brandSelect?.addEventListener('change', fetchFilteredData);

    form?.querySelectorAll('input[type="radio"][name="view"]').forEach(radio => {
        radio.addEventListener('change', fetchFilteredData);
    });

    let debounceTimer;
    searchInput?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchFilteredData, 400);
    });

    setInterval(fetchFilteredData, 30000);

    modal?.addEventListener('click', function (e) {
        if (e.target === this) closeBookingModal();
    });

    createModal?.addEventListener('click', function (e) {
        if (e.target === this) closeCreateBookingModal();
    });

    toggleCustomerType();

    document.getElementById('searchExistingCustomerBtn')?.addEventListener('click', async function () {
        const keyword = document.getElementById('existing_customer_search').value.trim();
        const resultBox = document.getElementById('existingCustomerResult');
        const notFoundBox = document.getElementById('existingCustomerNotFound');

        resultBox.classList.add('hidden');
        notFoundBox.classList.add('hidden');

        if (!keyword) {
            notFoundBox.textContent = 'Please enter email or phone first.';
            notFoundBox.classList.remove('hidden');
            return;
        }

        try {
            const response = await fetch("{{ route('admin.bookings.search-customer') }}?keyword=" + encodeURIComponent(keyword), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                document.getElementById('existing_user_id').value = '';
                notFoundBox.textContent = data.message || 'Customer not found.';
                notFoundBox.classList.remove('hidden');
                return;
            }

            document.getElementById('existing_user_id').value = data.user.id;
            document.getElementById('existing_customer_name').value = data.user.name ?? '';
            document.getElementById('existing_customer_phone').value = data.user.phone ?? '';
            document.getElementById('existing_customer_email').value = data.user.email ?? '';

            notFoundBox.classList.add('hidden');
            resultBox.classList.remove('hidden');
        } catch (error) {
            document.getElementById('existing_user_id').value = '';
            resultBox.classList.add('hidden');
            notFoundBox.textContent = 'Failed to search customer.';
            notFoundBox.classList.remove('hidden');
        }
    });

    document.getElementById('createBookingForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const form = e.target;
        const errorBox = document.getElementById('createBookingError');
        const successBox = document.getElementById('createBookingSuccess');

        errorBox.classList.add('hidden');
        successBox.classList.add('hidden');

        const formData = new FormData(form);

        try {
            const res = await fetch("{{ route('admin.bookings.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                errorBox.textContent = data.message || 'Failed to create booking.';
                errorBox.classList.remove('hidden');
                return;
            }

            successBox.textContent = data.message || 'Booking created successfully.';
            successBox.classList.remove('hidden');

            setTimeout(() => {
                closeCreateBookingModal();
                fetchFilteredData();
            }, 900);
        } catch (error) {
            console.error(error);
            errorBox.textContent = 'Something went wrong while creating the booking.';
            errorBox.classList.remove('hidden');
        }
    });
});

function openBookingModal(bookingId) {
    const modal = document.getElementById('bookingModal');
    const content = document.getElementById('bookingModalContent');

    modal.classList.remove('hidden');

    content.innerHTML = `
        <div class="text-center text-gray-400 py-4">
            <i class="fas fa-spinner fa-spin mr-2"></i>Loading booking details...
        </div>`;

    fetch(`/admin/bookings/${bookingId}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    })
    .then(data => {
        const isConfirmed = ['confirmed', 'approved'].includes((data.status || '').toLowerCase());
        const statusBadge = isConfirmed ? 'bg-red-500' : 'bg-amber-500';

        content.innerHTML = `
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="px-3 py-1 rounded-full text-white ${statusBadge}">
                        ${data.status ?? 'N/A'}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Car</span>
                    <span class="font-semibold">${data.car?.name ?? 'N/A'}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Customer</span>
                    <span>${data.user?.name ?? 'N/A'}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Email</span>
                    <span>${data.user?.email ?? 'N/A'}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Phone</span>
                    <span>${data.user?.phone ?? 'N/A'}</span>
                </div>

                <div class="border-t pt-3">
                    <p><strong>Pickup:</strong> ${data.pickup_at ?? 'N/A'}</p>
                    <p><strong>Return:</strong> ${data.return_at ?? 'N/A'}</p>
                    <p><strong>Service Type:</strong> ${data.service_type ?? 'N/A'}</p>
                    <p><strong>Location:</strong> ${data.service_location ?? 'N/A'}</p>
                </div>

                <div class="border-t pt-3 text-right font-bold text-lg">
                    ₱${data.total_price ?? '0.00'}
                </div>
            </div>`;
    })
    .catch(() => {
        content.innerHTML = `
            <div class="text-red-500 text-center py-4">
                <i class="fas fa-exclamation-circle mr-2"></i>Failed to load booking details.
            </div>`;
    });
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}

function openCreateBookingModal(carId, date, carName) {
    document.getElementById('modal_car_id').value = carId;
    document.getElementById('modal_car_name').value = carName;
    document.getElementById('modal_pickup_date').value = date;
    document.getElementById('modal_return_date').value = date;

    document.getElementById('createBookingError').classList.add('hidden');
    document.getElementById('createBookingSuccess').classList.add('hidden');
    document.getElementById('createBookingModal').classList.remove('hidden');

    const defaultRadio = document.querySelector('input[name="customer_type"][value="new"]');
    if (defaultRadio) defaultRadio.checked = true;
    toggleCustomerType();
}

function closeCreateBookingModal() {
    const form = document.getElementById('createBookingForm');
    form.reset();

    document.getElementById('createBookingModal').classList.add('hidden');
    document.getElementById('createBookingError').classList.add('hidden');
    document.getElementById('createBookingSuccess').classList.add('hidden');
    document.getElementById('existingCustomerResult').classList.add('hidden');
    document.getElementById('existingCustomerNotFound').classList.add('hidden');
    document.getElementById('existing_user_id').value = '';

    const defaultRadio = document.querySelector('input[name="customer_type"][value="new"]');
    if (defaultRadio) defaultRadio.checked = true;
    toggleCustomerType();
}
</script>