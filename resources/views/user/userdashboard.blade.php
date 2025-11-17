<!-- resources/views/user/dashboard.blade.php -->
@extends('layouts.userlayout')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-56 bg-white shadow-md transition-all duration-300 flex flex-col border-r border-gray-200">
        <div class="p-5 flex items-center justify-between border-b border-gray-200">
            <h1 class="font-bold text-lg">🚗 CarRental Pro</h1>
            <button id="toggleSidebar" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-3 space-y-2">
            <a href="{{ route('user.dashboard') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-black text-white transition">
                <span class="text-lg">📊</span>
                <span class="menu-label">Dashboard</span>
            </a>
            <a href="{{ route('user.rentals') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">🚗</span>
                <span class="menu-label">My Rentals</span>
            </a>
            <a href="{{ route('user.browse') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">🔍</span>
                <span class="menu-label">Browse Cars</span>
            </a>
            <a href="{{ route('user.history') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">📜</span>
                <span class="menu-label">Rental History</span>
            </a>
            <a href="{{ route('user.payments') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">💳</span>
                <span class="menu-label">Payments</span>
            </a>
            <a href="{{ route('user.profile') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">👤</span>
                <span class="menu-label">Profile</span>
            </a>
            <a href="{{ route('user.settings') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">⚙️</span>
                <span class="menu-label">Settings</span>
            </a>
        </nav>

        <div class="p-3 border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    <span class="text-lg">🚪</span>
                    <span class="menu-label">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center border-b border-gray-200">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
                <p class="text-gray-600 text-sm">Welcome back! Here's your rental overview.</p>
            </div>
            <div class="flex items-center space-x-4">
                <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <div class="flex items-center space-x-3 pl-4 border-l border-gray-200">
                    <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Customer</p>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Active Rentals -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Active Rentals</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $activeRentals ?? 0 }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Spent</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">&#8369;{{ number_format($totalSpent ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-green-100">
                             <i class="fas fa-peso-sign text-green-500 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Completed Trips -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Complete Booking</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $completedTrips ?? 0 }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-purple-100">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Loyalty Points -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Loyalty Points</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $loyaltyPoints ?? 0 }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Rentals and Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Current Rentals -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Current Rentals</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($currentRentals ?? [] as $rental)
                            <div class="flex items-center gap-4 pb-4 border-b border-gray-200 last:border-0">
                      <div class="aspect-[16/9] w-32 rounded overflow-hidden bg-gray-200 flex items-center justify-center">  
                             @if($rental->car->images && count(json_decode($rental->car->images)) > 0)
                             @php
                            $images = json_decode($rental->car->images);
                             $firstImage = $images[0];
                         @endphp
                              <img src="{{ asset('storage/' . $firstImage) }}" alt="Car Image" class="w-full h-full object-cover">
                             @else
                               <span class="text-2xl">🚙</span>
                                @endif
                            </div>           
                        <div class="flex-1">
                                    <h4 class="font-bold text-gray-900">{{ $rental->car->brand->name ?? 'Car' }} {{ $rental->car->model ?? '' }}</h4>
                                    <p class="text-sm text-gray-600">Pickup: {{ $rental->pickup_at->format('M d, Y') }} • Return: {{ $rental->return_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">&#8369;{{ number_format($rental->total_price, ) }}/day</p>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $rental->status->id == 2 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $rental->status->name ?? 'Active' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No active rentals</p>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('user.browse') }}" class="w-full flex items-center justify-center bg-black text-white px-4 py-3 rounded-lg font-medium hover:bg-gray-900 transition">
                            <span class="mr-2">+</span> New Rental
                        </a>
                        <button class="w-full flex items-center justify-center border border-gray-300 px-4 py-3 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">
                            <span class="mr-2">📅</span> Extend Rental
                        </button>
                        <button class="w-full flex items-center justify-center border border-gray-300 px-4 py-3 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">
                            <span class="mr-2">💬</span> Contact Support
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Activity and Upcoming -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Recent Activity</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($recentActivity ?? [] as $activity)
                            <div class="flex items-start gap-3">
                                <span class="text-2xl">•</span>
                                <div>
                                    <p class="text-gray-900 font-medium">{{ $activity->activity ?? 'Activity' }}</p>
                                    <p class="text-sm text-gray-500">{{ $activity->time ?? 'Recently' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No recent activity</p>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Upcoming</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($upcomingEvents ?? [] as $event)
                            <div class="flex items-center justify-between pb-4 border-b border-gray-200 last:border-0">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $event->event ?? 'Event' }}</p>
                                    <p class="text-sm text-gray-500">{{ $event->date ?? 'Date TBA' }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No upcoming events</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<style>
    .menu-label {
        transition: opacity 0.3s ease;
    }
</style>

@endsection
@section('scripts')
<script src="{{ asset('js/user/userdashboard.js') }}"></script>

@endsection