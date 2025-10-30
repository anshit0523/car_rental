<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex flex-col lg:flex-row min-h-screen">
      
        <!-- Sidebar -->
        <div class="fixed lg:relative top-0 left-0 z-50 h-screen w-64 bg-gray-900 text-white p-6 overflow-y-auto lg:block hidden">
            <div class="mb-8 flex items-center gap-2 text-xl font-bold">
                <i class="fas fa-car"></i>
                <span>Car Rental</span>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white/20">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition">
                    <i class="fas fa-calendar-check w-5"></i>
                    <span>Bookings</span>
                </a>
                <a href="{{ route('admin.cars') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition">
                    <i class="fas fa-car w-5"></i>
                    <span>Fleet</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition">
                    <i class="fas fa-users w-5"></i>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.revenue') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span>Revenue</span>
                </a>
            </nav>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="lg:hidden bg-gray-900 text-white p-4 flex items-center justify-between">
            <h1 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-car"></i>
                <span>Car Rental</span>
            </h1>
            <button class="text-2xl" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden fixed top-0 left-0 w-full h-screen bg-gray-900 text-white z-40 p-6 lg:hidden">
            <button class="absolute top-4 right-4 text-2xl" id="menuClose">
                <i class="fas fa-times"></i>
            </button>
            <div class="mt-8">
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-white/20 block">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition block">
                        <i class="fas fa-calendar-check w-5"></i>
                        <span>Bookings</span>
                    </a>
                    <a href="{{ route('admin.cars') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition block">
                        <i class="fas fa-car w-5"></i>
                        <span>Fleet</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition block">
                        <i class="fas fa-users w-5"></i>
                        <span>Users</span>
                    </a>
                    <a href="{{ route('admin.revenue') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:bg-white/10 transition block">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Revenue</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto w-full lg:ml-0">
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
        </div>
    </div>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const menuClose = document.getElementById('menuClose');
        const mobileMenu = document.getElementById('mobileMenu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
        });

        menuClose.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });

        // Close menu when clicking on a link
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });

        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    {
                        label: 'Bookings',
                        data: {!! json_encode($bookingsData) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue ($)',
                        data: {!! json_encode($revenueData) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Bookings'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statusLabels) !!},
                datasets: [{
                    data: {!! json_encode($statusData) !!},
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#3b82f6'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>