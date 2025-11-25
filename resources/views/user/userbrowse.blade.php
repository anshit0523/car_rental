@extends('layouts.userlayout')

@section('content')
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-56 bg-white shadow-md transition-all duration-300 flex flex-col border-r border-gray-200">
        <div class="p-5 flex items-center justify-between border-b border-gray-200">
            <h1 class="font-bold text-lg">Car Rental</h1>
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
            <a href="{{ route('user.browse') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-black text-white transition">
                <span class="text-lg">🔍</span>
                <span class="menu-label">Browse Cars</span>
            </a>
            <a href="{{ route('user.rentals') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">📜</span>
                <span class="menu-label">Rental History</span>
            </a>
            <a href="{{ route('user.rentals') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">💳</span>
                <span class="menu-label">Payments</span>
            </a>
            <a href="{{ route('user.rentals') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <span class="text-lg">👤</span>
                <span class="menu-label">Profile</span>
            </a>
            <a href="{{ route('user.rentals') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
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
        <div class="bg-white shadow-sm border-b border-gray-200 p-1">
            <!-- Search Section -->
            <div class="bg-gray-50 p-3 rounded-lg">
                <form action="{{ route('user.search') }}" method="POST" class="grid grid-cols-5 gap-3">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Pick-up Date</label>
                        <input type="date" name="pickup_date" class="w-full border border-gray-300 rounded px-2 py-2 text-xs" value="{{ request('pickup_date') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Return Date</label>
                        <input type="date" name="return_date" class="w-full border border-gray-300 rounded px-2 py-2 text-xs" value="{{ request('return_date') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Time</label>
                        <select name="time" class="w-full border border-gray-300 rounded px-2 py-2 text-xs">
                            <option value="">Select Time</option>
                            <option value="10:00" @selected(request('time') == '10:00')>10:00 AM</option>
                            <option value="11:00" @selected(request('time') == '11:00')>11:00 AM</option>
                            <option value="12:00" @selected(request('time') == '12:00')>12:00 PM</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-black text-white rounded py-2 font-semibold hover:bg-gray-800 text-xs flex items-center justify-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Search</span>
                        </button>
                    </div>
                    <div class="flex items-center space-x-4 pl-8 border-l border-gray-300">
                    <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block ">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Customer</p>
                    </div>
                </div>
                </form>
            </div>
        </div>

            <!-- Content Area -->
            <div class="flex-1 p-4 lg:p-6 overflow-y-auto">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Main Content -->
                    <div class="flex-1">
                        <!-- Title and Sort -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Available Cars</h2>
                                <p class="text-gray-600 text-sm">{{ $totalCount }} cars found</p>
                            </div>
                            <form action="{{ route('user.browse') }}" method="GET" class="w-full sm:w-auto">
                                <select name="sort_by" onchange="this.form.submit()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-semibold">
                                    <option value="price_low" @selected(request('sort_by') == 'price_low')>Sort by: Price (Low to High)</option>
                                    <option value="price_high" @selected(request('sort_by') == 'price_high')>Price (High to Low)</option>
                                    <option value="newest" @selected(request('sort_by') == 'newest')>Newest</option>
                                </select>
                            </form>
                        </div>

                        <!-- Cars Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-8">
                            @forelse($cars as $car)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                    <!-- Car Image -->
                                    <div class="h-48 bg-gray-300 flex items-center justify-center overflow-hidden">
                                        @if($car->images && count(json_decode($car->images)) > 0)
                                            @php
                                                $images = json_decode($car->images);
                                                $firstImage = $images[0];
                                            @endphp
                                            <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $car->model }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="flex flex-col items-center justify-center text-white">
                                                <i class="fas fa-image text-4xl mb-2"></i>
                                                <p class="text-sm">No Image</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Car Details -->
                                    <div class="p-4">
                                        <h3 class="text-lg font-bold text-gray-900 mb-3">
                                            {{ $car->brand->name ?? 'N/A' }} {{ $car->model }}
                                        </h3>

                                        <div class="flex flex-wrap gap-2 text-sm text-gray-600 mb-4">
                                            <span>🧑‍🤝‍🧑 {{ $car->seats }} Seats</span>
                                            <span>⚙️ {{ $car->transmission?->type ?? 'Manual' }}</span>
                                            <span>⛽ {{ $car->fuelType?->type ?? 'Petrol' }}</span>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-2xl font-bold text-gray-900">
                                                    &#8369;{{ number_format($car->price_per_day) }}
                                                </p>
                                                <p class="text-gray-600 text-sm">/day</p>
                                            </div>
                                            <a href="{{ route('user.car-detail', [
                                                'id' => $car->id,
                                                'pickup_date' => request('pickup_date'),
                                                'return_date' => request('return_date'),
                                                'time' => request('time')
                                            ]) }}"
                                                class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 font-semibold text-sm transition">
                                                Rent Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-12">
                                    <p class="text-gray-600 text-lg">No cars available matching your criteria</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        <div class="flex items-center justify-center">
                            {{ $cars->links() }}
                        </div>
                    </div>

                    <!-- Filters Sidebar (Right) -->
                    <div class="w-full lg:w-64 bg-white rounded-lg shadow-md p-4 lg:p-6 h-fit">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Filters</h3>

                        <form action="{{ route('user.browse') }}" method="GET" id="filterForm" class="space-y-6">
                            <!-- Price Range -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3">Price Range</h4>
                                <input type="range" name="max_price" min="0" max="8000"
                                    value="{{ request('max_price', 8000) }}" class="w-full cursor-pointer"
                                    onchange="document.getElementById('filterForm').submit()">
                                <div class="flex items-center justify-between text-sm text-gray-600 mt-2">
                                    <span>₱0</span>
                                    <span>₱{{ request('max_price', 8000) }}</span>
                                </div>
                            </div>

                            <!-- Brand -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3">Brand</h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($brands as $brand)
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="brand_id[]" value="{{ $brand->id }}"
                                                class="w-4 h-4 rounded cursor-pointer"
                                                @checked(in_array($brand->id, (array) request('brand_id', [])))
                                                onchange="document.getElementById('filterForm').submit()">
                                            <span class="ml-3 text-gray-700">{{ $brand->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Fuel Type -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3">Fuel Type</h4>
                                <div class="space-y-2">
                                    @foreach($fuelTypes as $fuelType)
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="fuel_type_id[]" value="{{ $fuelType->id }}"
                                                class="w-4 h-4 rounded cursor-pointer"
                                                @checked(in_array($fuelType->id, (array) request('fuel_type_id', [])))
                                                onchange="document.getElementById('filterForm').submit()">
                                            <span class="ml-3 text-gray-700">{{ $fuelType->type }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Transmission -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-3">Transmission</h4>
                                <div class="space-y-2">
                                    @foreach($transmissions as $transmission)
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="transmission_id[]" value="{{ $transmission->id }}"
                                                class="w-4 h-4 rounded cursor-pointer"
                                                @checked(in_array($transmission->id, (array) request('transmission_id', [])))
                                                onchange="document.getElementById('filterForm').submit()">
                                            <span class="ml-3 text-gray-700">{{ $transmission->type }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Clear Filters -->
                            <a href="{{ route('user.browse') }}"
                                class="w-full px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-semibold text-center block">
                                Clear All Filters
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
   
@endsection

@section('scripts')
    <script src="{{ asset('js/user/userbrowsecar.js') }}"></script>
@endsection