@extends('layouts.adminlayout')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 p-4 md:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
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

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <!-- Filters Section -->
            <div class="p-4 md:p-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <form method="GET" class="space-y-6">
                    <!-- Top Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Brand Filter -->
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

                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-blue-600 mr-2"></i>Search
                            </label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="License plate, model..." value="{{ request('search') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 transition">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Date Navigation -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>Navigation
                            </label>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.calendar', ['date' => $previousWeek]) }}" class="p-2.5 hover:bg-gray-100 rounded-lg transition text-gray-600 hover:text-blue-600">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <span class="text-sm font-medium text-gray-700 px-3 py-2 bg-gray-50 rounded-lg min-w-fit">
                                    {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
                                </span>
                                <a href="{{ route('admin.calendar', ['date' => $nextWeek]) }}" class="p-2.5 hover:bg-gray-100 rounded-lg transition text-gray-600 hover:text-blue-600">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                                <a href="{{ route('admin.calendar') }}" class="ml-auto px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    Today
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex items-center gap-4 pt-2 border-t border-gray-200">
                        <span class="text-sm font-semibold text-gray-700">View:</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="view" value="weekly" {{ request('view', 'weekly') == 'weekly' ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 text-blue-600 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">7 Days</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="view" value="30days" {{ request('view') == '30days' ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 text-blue-600 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition">30 Days</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Calendar Table -->
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

                                <!-- Calendar Cells -->
                                @foreach($calendarDates as $date)
                                    <td class="px-3 py-2 text-center">
                                        @php
                                            // Format the current date to Y-m-d for comparison
                                            $currentDate = \Carbon\Carbon::parse($date['full'])->format('Y-m-d');
                                            $cellBooking = null;
                                            $bgColor = 'bg-emerald-100';
                                            $icon = 'fa-check-circle';
                                            $label = 'Available';

                                            foreach ($car->bookings as $b) {
                                                $status = strtolower($b->status->name);
                                                
                                                // Format pickup and return dates to Y-m-d for consistent comparison
                                                $pickupDate = \Carbon\Carbon::parse($b->pickup_at)->format('Y-m-d');
                                                $returnDate = \Carbon\Carbon::parse($b->return_at)->format('Y-m-d');
                                                
                                                // Check if the current date falls within the booking period
                                                if (in_array($status, ['confirmed', 'reserved', 'pending', 'approved']) &&
                                                    $pickupDate <= $currentDate &&
                                                    $returnDate >= $currentDate) {
                                                    $cellBooking = $b;
                                                    
                                                    if (in_array($status, ['confirmed', 'approved'])) {
                                                        $bgColor = 'bg-red-500';
                                                        $icon = 'fa-lock';
                                                        $label = 'Booked';
                                                    } elseif (in_array($status, ['reserved', 'pending'])) {
                                                        $bgColor = 'bg-amber-500';
                                                        $icon = 'fa-hourglass-half';
                                                        $label = 'Reserved';
                                                    }
                                                    break;
                                                }
                                            }
                                        @endphp

                                        @if($cellBooking && in_array(strtolower($cellBooking->status->name), ['confirmed', 'reserved', 'pending', 'approved']))
                                            <button onclick="openBookingModal({{ $cellBooking->id }})" class="inline-flex items-center gap-1 px-3 py-2 {{ $bgColor }} text-white rounded-lg text-xs font-semibold shadow-md hover:shadow-lg transition transform hover:scale-105 cursor-pointer">
                                                <i class="fas {{ $icon }} text-xs"></i>
                                                <span>{{ $label }}</span>
                                            </button>
                                        @else
                                            <div class="inline-flex items-center gap-1 px-3 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Available</span>
                                            </div>
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

            <!-- Legend -->
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

<!-- Booking Details Modal -->
<div id="bookingModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 animate-fade-in">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-bold text-gray-800">
                Booking Details
            </h2>
            <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div id="bookingModalContent" class="px-6 py-4 space-y-4">
            <!-- Dynamic content will be injected here -->
            <div class="text-center text-gray-400">
                Loading booking details...
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t flex justify-end gap-3">
            <button onclick="closeBookingModal()"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Close
            </button>

            <!-- These buttons will be toggled later -->
            <button id="confirmBtn"
                    class="hidden px-4 py-2 text-sm rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                Confirm Booking
            </button>

            <button id="cancelBtn"
                    class="hidden px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700">
                Cancel Booking
            </button>
        </div>
    </div>
</div>


<script>
function refreshCalendar() {
    fetch(window.location.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newCalendar = doc.querySelector('table');
        const currentCalendar = document.querySelector('table');

        if (newCalendar && currentCalendar) {
            currentCalendar.innerHTML = newCalendar.innerHTML;
        }
    })
    .catch(err => console.error('Calendar refresh failed', err));
}

// Auto-refresh every 30 seconds
setInterval(refreshCalendar, 30000);


function openBookingModal(bookingId) {
    const modal = document.getElementById('bookingModal');
    const content = document.getElementById('bookingModalContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    content.innerHTML = `
        <div class="text-center text-gray-400">
            Loading booking details...
        </div>
    `;

    fetch(`/admin/bookings/${bookingId}`)
        .then(res => res.json())
        .then(data => {
            const statusBadge =
                data.status === 'Confirmed'
                    ? 'bg-red-500'
                    : 'bg-amber-500';

            content.innerHTML = `
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="px-3 py-1 rounded-full text-white ${statusBadge}">
                            ${data.status}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Car</span>
                        <span class="font-semibold">${data.car.name}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Customer</span>
                        <span>${data.user.name}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Email</span>
                        <span>${data.user.email}</span>
                    </div>

                    <div class="border-t pt-3">
                        <p><strong>Pickup:</strong> ${data.pickup_at}</p>
                        <p><strong>Return:</strong> ${data.return_at}</p>
                    </div>

                    <div class="border-t pt-3 text-right font-bold text-lg">
                        ₱${data.total_price}
                    </div>
                </div>
            `;
        })
        .catch(() => {
            content.innerHTML = `
                <div class="text-red-500 text-center">
                    Failed to load booking details.
                </div>
            `;
        });
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

</script>