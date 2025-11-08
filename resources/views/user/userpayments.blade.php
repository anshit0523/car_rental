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
            <a href="{{ route('user.dashboard') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
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
            <a href="{{ route('user.history') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-black text-white transition">
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

    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="#" class="hover:text-gray-900">Home</a>
            <span>›</span>
            <a href="#" class="hover:text-gray-900">Search Results</a>
            <span>›</span>
            <span class="text-gray-900 font-semibold">Car Details</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid grid-cols-3 gap-8">
            <!-- Left Section - Car Details -->
            <div class="col-span-2">
                <!-- Main Image -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="relative bg-gray-300 h-96 flex items-center justify-center">
                        <img src="https://images.unsplash.com/photo-1567818735868-e71b99932e29?w=600&h=400&fit=crop" alt="BMW 5 Series" class="w-full h-full object-cover">
                        <button class="absolute top-4 right-4 p-2 bg-white rounded-full shadow-md hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <button class="absolute bottom-4 left-4 p-2 bg-white rounded-full shadow-md hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button class="absolute bottom-4 right-4 p-2 bg-white rounded-full shadow-md hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Thumbnail Images -->
                    <div class="p-4 flex space-x-3">
                        <div class="w-20 h-20 bg-gray-300 rounded-lg overflow-hidden cursor-pointer hover:opacity-80">
                            <img src="https://images.unsplash.com/photo-1567818735868-e71b99932e29?w=100&h=100&fit=crop" alt="Thumb" class="w-full h-full object-cover">
                        </div>
                        <div class="w-20 h-20 bg-gray-300 rounded-lg overflow-hidden cursor-pointer hover:opacity-80">
                            <img src="https://images.unsplash.com/photo-1560958089-b8a63dd8b50?w=100&h=100&fit=crop" alt="Thumb" class="w-full h-full object-cover">
                        </div>
                        <div class="w-20 h-20 bg-gray-300 rounded-lg overflow-hidden cursor-pointer hover:opacity-80">
                            <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=100&h=100&fit=crop" alt="Thumb" class="w-full h-full object-cover">
                        </div>
                        <div class="w-20 h-20 bg-gray-300 rounded-lg overflow-hidden cursor-pointer hover:opacity-80">
                            <img src="https://images.unsplash.com/photo-1566023967268-de5d93b94088?w=100&h=100&fit=crop" alt="Thumb" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <!-- Car Title and Rating -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">BMW 5 Series 530i</h1>
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center">
                                    <span class="text-yellow-400">★</span>
                                    <span class="text-yellow-400">★</span>
                                    <span class="text-yellow-400">★</span>
                                    <span class="text-yellow-400">★</span>
                                    <span class="text-gray-300">★</span>
                                </div>
                                <span class="text-gray-700 font-semibold">4.8 (124 reviews)</span>
                                <span class="text-gray-500">Downtown Location</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-4xl font-bold text-gray-900">$89</p>
                            <p class="text-gray-600">/day</p>
                        </div>
                    </div>
                </div>

                <!-- Key Features -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">🧑‍🤝‍🧑</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">Passengers</p>
                            <p class="text-gray-600">5 People</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">⚙️</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">Transmission</p>
                            <p class="text-gray-600">Automatic</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">⛽</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">Fuel Type</p>
                            <p class="text-gray-600">Gasoline</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">🧳</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">Luggage</p>
                            <p class="text-gray-600">3 Bags</p>
                        </div>
                    </div>
                </div>

                <!-- Features & Amenities -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Features & Amenities</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            <span class="text-gray-700">Air Conditioning</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            <span class="text-gray-700">GPS Navigation</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            <span class="text-gray-700">USB Charging</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            <span class="text-gray-700">Bluetooth</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            <span class="text-gray-700">Leather Seats</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            <span class="text-gray-700">Sunroof</span>
                        </div>
                    </div>
                </div>

                <!-- Rental Terms & Conditions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Rental Terms & Conditions</h2>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">Minimum age requirement: 25 years old</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">Valid driver's license required for at least 2 years</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">Security deposit of $500 will be held on your card</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                </svg>
                            </div>
                            <span class="text-gray-700">Free cancellation up to 24 hours before pickup</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section - Booking Form -->
            <div class="col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Book This Car</h2>

                    <!-- Pickup Date & Time -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Pickup Date & Time</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" placeholder="dd/mm/yyyy" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <svg class="w-5 h-5 text-gray-400 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="text" placeholder="-- : -- --" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-2">
                    </div>

                    <!-- Return Date & Time -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Return Date & Time</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" placeholder="dd/mm/yyyy" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <svg class="w-5 h-5 text-gray-400 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="text" placeholder="-- : -- --" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-2">
                    </div>

                    <!-- Pickup Location -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Pickup Location</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option>Downtown Office - 123 Main St</option>
                            <option>Airport Terminal</option>
                            <option>Downtown Office - 123 Main St</option>
                        </select>
                    </div>

                    <!-- Pricing Breakdown -->
                    <div class="border-t border-gray-200 pt-4 mb-6 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Daily rate (3 days)</span>
                            <span class="text-gray-900 font-semibold">$267.00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Insurance</span>
                            <span class="text-gray-900 font-semibold">$45.00</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Taxes & fees</span>
                            <span class="text-gray-900 font-semibold">$23.40</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-green-600 text-sm font-semibold">Discount (10%)</span>
                            <span class="text-green-600 font-semibold">-$28.70</span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-900 font-bold">Total</span>
                            <span class="text-2xl font-bold text-gray-900">$308.70</span>
                        </div>
                    </div>

                    <!-- Confirm Button -->
                    <button class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition mb-3">
                        Confirm Booking
                    </button>

                    <!-- Cancellation Policy -->
                    <p class="text-xs text-center text-gray-500">
                        Free cancellation • No hidden fees
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection