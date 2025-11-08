@extends('layouts.userlayout')

@section('content')
<div class="flex h-screen bg-gray-100">
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
            <a href="{{ route('user.dashboard') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('user.dashboard') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
                <span class="text-lg">📊</span>
                <span class="menu-label">Dashboard</span>
            </a>
            <a href="{{ route('user.rentals') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('user.rentals') ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-100' }} transition">
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
    <div class="flex-1 overflow-auto flex flex-col">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Active Rentals</h1>
                
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white border-b border-gray-200 px-6">
            <div class="flex space-x-1">
                <a href="#" class="px-6 py-4 font-medium text-blue-600 border-b-2 border-blue-600 transition">Active Rentals</a>
                <a href="#" class="px-6 py-4 font-medium text-gray-500 hover:text-gray-700 transition">Upcoming</a>
                <a href="#" class="px-6 py-4 font-medium text-gray-500 hover:text-gray-700 transition">Completed</a>
                <a href="#" class="px-6 py-4 font-medium text-gray-500 hover:text-gray-700 transition">Cancelled</a>
            </div>
        </div>

        <!-- Rentals Content -->
        <div class="flex-1 p-6 space-y-6">
            <!-- BMW 5 Series Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <div class="flex flex-col md:flex-row md:h-50">
                    <!-- Image -->
                    <div class="md:w-45 h-40 overflow-hidden flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1567818735868-e71b99932e29?w=400&h=300&fit=crop" alt="BMW 5 Series" class="w-full h-full object-cover">
                    </div>

                    <!-- Content -->
                    <div class="flex-1 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h3 class="text-lg font-bold text-gray-900">BMW 5 Series</h3>
                                        <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-semibold">Active</span>
                                    </div>
                                    <p class="text-gray-600 text-xs mb-3">Sedan • Automatic • Premium</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-gray-900">$89</p>
                                    <p class="text-gray-500 text-xs">/day</p>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-3 gap-3 mb-3 text-xs">
                                <div class="flex items-center space-x-2">
                                    <span class="text-blue-600">📅</span>
                                    <div>
                                        <p class="text-gray-500">Nov 5, 2025</p>
                                        <p class="font-semibold text-gray-900">Pick-up</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-blue-600">📅</span>
                                    <div>
                                        <p class="text-gray-500">Nov 10, 2025</p>
                                        <p class="font-semibold text-gray-900">Return</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-blue-600">📍</span>
                                    <div>
                                        <p class="text-gray-500">5 days</p>
                                        <p class="font-semibold text-gray-900">Duration</p>
                                    </div>
                                </div>
                            </div>

                            <p class="text-gray-600 text-xs">📍 Downtown Station, 245 Park Avenue, New York</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2 mt-3">
                            <button class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold flex items-center justify-center space-x-1">
                                <span>✓</span>
                                <span>View Details</span>
                            </button>
                            <button class="flex-1 px-3 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm font-semibold">
                                Contact Support
                            </button>
                            <button class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition text-sm font-semibold">
                                Cancel Booking
                            </button>
                        </div>
                    </div>
                </div>
            </div>

           

          
        </div>
    </div>
</div>

<script>
    document.getElementById('toggleSidebar').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('w-56');
        sidebar.classList.toggle('w-0');
    });
</script>
@endsection