@extends('layouts.adminlayout')

@section('content')
<div class="p-6 lg:p-8">
 
      <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Bookings Card -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Bookings</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalBookings }}</p>
                                <p class="text-green-600 text-sm mt-2">↑ {{ $bookingsTrend }}% this month</p>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100">
                                <i class="fas fa-calendar-check text-blue-500 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Revenue Card -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($totalRevenue, 2) }}</p>
                                <p class="text-green-600 text-sm mt-2">↑ {{ $revenueTrend }}% this month</p>
                            </div>
                            <div class="p-3 rounded-full bg-green-100">
                                <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Fleet Card -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Active Fleet</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $activeCars }}</p>
                                <p class="text-green-600 text-sm mt-2">Out of {{ $totalCars }} cars</p>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100">
                                <i class="fas fa-car text-yellow-500 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Users Card -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalUsers }}</p>
                                <p class="text-green-600 text-sm mt-2">↑ {{ $usersTrend }}% this month</p>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100">
                                <i class="fas fa-users text-purple-500 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Bookings & Revenue Chart -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Bookings & Revenue Trend</h3>
                        <canvas id="trendChart"></canvas>
                    </div>

                    <!-- Booking Status Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Booking Status</h3>
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Recent Bookings Table -->
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentBookings as $booking)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $booking->user->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->car->brand->name ?? 'N/A' }} {{ $booking->car->model ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->pickup_at->format('M d, Y') ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->return_at->format('M d, Y') ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">${{ number_format($booking->total_price, 2) }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                @if($booking->status->id == 1) bg-green-100 text-green-800
                                                @elseif($booking->status->id == 2) bg-blue-100 text-blue-800
                                                @elseif($booking->status->id == 3) bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $booking->status->name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No bookings found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> 
            @endsection

@section('scripts')
<script>
    window.dashboardData = {
        months: {!! json_encode($months) !!},
        bookingsData: {!! json_encode($bookingsData) !!},
        revenueData: {!! json_encode($revenueData) !!},
        statusLabels: {!! json_encode($statusLabels) !!},
        statusData: {!! json_encode($statusData) !!}
    };
</script>

<script src="{{ asset('js/admindashboard.js') }}"></script>

@endsection


    


