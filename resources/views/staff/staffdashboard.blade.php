@extends('layouts.adminlayout')

@section('content')
<div class="flex h-screen overflow-hidden">
    <div class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100">
        <div class="p-6 lg:p-8">

            <div class="mb-8">
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Staff Dashboard</h1>
                <p class="text-gray-600">Daily operations overview</p>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <p class="text-gray-600 text-sm font-medium">Pending Bookings</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingBookings }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <p class="text-gray-600 text-sm font-medium">Pending Payments</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingPayments }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <p class="text-gray-600 text-sm font-medium">Active Rentals</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $activeRentals }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                    <p class="text-gray-600 text-sm font-medium">Returns Today</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $returnsToday }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                    <p class="text-gray-600 text-sm font-medium">Need Attention</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $attentionCars }}</p>
                </div>
            </div>

            <!-- Quick Tables -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">

                <!-- Today's Pickups -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Today’s Pickups</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Car</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Pickup</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($todayPickups as $booking)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $booking->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $booking->car->brand->name ?? 'N/A' }} {{ $booking->car->model ?? '' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ optional($booking->pickup_at)->format('M d, Y h:i A') ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $booking->status->name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No pickups today</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Today's Returns -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Today’s Returns</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Car</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Return</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($todayReturns as $booking)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $booking->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $booking->car->brand->name ?? 'N/A' }} {{ $booking->car->model ?? '' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ optional($booking->return_at)->format('M d, Y h:i A') ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $booking->status->name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No returns today</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Recent Bookings</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Car</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Pickup</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Return</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentBookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $booking->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $booking->car->brand->name ?? 'N/A' }} {{ $booking->car->model ?? '' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ optional($booking->pickup_at)->format('M d, Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ optional($booking->return_at)->format('M d, Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $statusName = $booking->status->name ?? 'Unknown';
                                        @endphp

                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @switch($statusName)
                                                @case('Pending') bg-gray-100 text-gray-800 @break
                                                @case('Reserved') bg-yellow-100 text-yellow-800 @break
                                                @case('Confirmed') bg-blue-100 text-blue-800 @break
                                                @case('Active') bg-indigo-100 text-indigo-800 @break
                                                @case('Return') bg-orange-100 text-orange-800 @break
                                                @case('Completed') bg-green-100 text-green-800 @break
                                                @case('Cancelled') bg-red-100 text-red-800 @break
                                                @case('Checkup') bg-purple-100 text-purple-800 @break
                                                @case('Damage') bg-rose-100 text-rose-800 @break
                                                @case('Needs Repair') bg-amber-100 text-amber-800 @break
                                                @case('Failed') bg-gray-300 text-gray-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ $statusName }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No recent bookings found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection