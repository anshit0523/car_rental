@extends('layouts.userlayout')

@section('content')
<div class="flex h-screen bg-gray-100">
    

    <!-- Main Content -->
    <div class="flex-1 overflow-auto flex flex-col">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">My Rentals</h1>
            </div>
        </div>

        <!-- Flash Messages -->
        @if ($errors->has('booking'))
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                {{ $errors->first('booking') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

         <!-- Tabs -->
       <div class="bg-white border-b border-gray-200 px-6">
    <div class="flex space-x-1">
        <a href="{{ route('user.rentals.active') }}"
           class="px-6 py-4 font-medium border-b-2 transition
           {{ request()->routeIs('user.rentals.active') ? 'text-blue-600 border-blue-600' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">
            Active Rentals
        </a>

        <a href="{{ route('user.rentals.upcoming') }}"
           class="px-6 py-4 font-medium border-b-2 transition
           {{ request()->routeIs('user.rentals.upcoming') ? 'text-blue-600 border-blue-600' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">
            Upcoming
        </a>

        <a href="{{ route('user.rentals.completed') }}"
           class="px-6 py-4 font-medium border-b-2 transition
           {{ request()->routeIs('user.rentals.completed') ? 'text-blue-600 border-blue-600' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">
            Completed
        </a>

        <a href="{{ route('user.rentals.cancelled') }}"
           class="px-6 py-4 font-medium border-b-2 transition
           {{ request()->routeIs('user.rentals.cancelled') ? 'text-blue-600 border-blue-600' : 'text-gray-500 hover:text-gray-700 border-transparent' }}">
            Cancelled
        </a>
    </div>
</div>

        
        <!-- Rentals Content -->
        <div class="flex-1 p-6 space-y-6">
            @forelse ($bookings as $booking)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="flex flex-col md:flex-row md:h-35">
                        <!-- Image -->
                      @php
    $images = $booking->car->images ? json_decode($booking->car->images, true) : [];
    $firstImage = $images[0] ?? null;
@endphp

<div class="w-[450px] h-[236px] overflow-hidden rounded">
    <img
        src="{{ $firstImage ? asset('storage/' . $firstImage) : 'https://images.unsplash.com/photo-1567818735868-e71b99932e29?w=400&h=300&fit=crop' }}"
        alt="{{ $booking->car->model }}"
        class="w-full h-full object-cover"
    >
</div>


                        <!-- Content -->
                        <div class="flex-1 p-5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center space-x-2 mb-1">
                                            <h3 class="text-lg font-bold text-gray-900">
                                            {{ $booking->car->brand->name }} {{ $booking->car->model }}</h3>
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold
                                                @if ($booking->status->name === 'Active')
                                                    bg-green-100 text-green-700
                                                @elseif ($booking->status->name === 'Upcoming')
                                                    bg-blue-100 text-blue-700
                                                @elseif ($booking->status->name === 'Cancelled')
                                                    bg-red-100 text-red-700
                                                @else
                                                    bg-gray-100 text-gray-700
                                                @endif
                                            ">
                                                {{ $booking->status->name }}
                                            </span>
                                        </div>
                                       <p class="text-gray-900 text-xs mb-12"> 
                                        {{ $booking->car->fuelType->type }} • {{ $booking->car->transmission->type }} 
                                       </p>

                                    </div>
                                    <div class="text-right " >
                                        <p class="text-2xl font-bold text-gray-900">&#8369;{{ number_format($booking->car->price_per_day, ) }}</p>
                                        <p class="text-gray-500 text-xs">/day</p>
                                    </div>
                                </div>

                                <!-- Details Grid -->
                                <div class="grid grid-cols-3 gap-3 mt-5 text-xs">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-blue-600">📅</span>
                                        <div>
                                            <p class="text-gray-500">{{ $booking->pickup_at ? $booking->pickup_at->format('M d, Y') : 'N/A' }}</p>
                                            <p class="font-semibold text-gray-900">Pick-up</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-blue-600">📅</span>
                                        <div>
                                            <p class="text-gray-500">{{ $booking->return_at ? $booking->return_at->format('M d, Y') : 'N/A' }}</p>
                                            <p class="font-semibold text-gray-900">Return</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-blue-600">📍</span>
                                        <div>
                                            <p class="text-gray-500">{{ $booking->pickup_at && $booking->return_at ? $booking->pickup_at->diffInDays($booking->return_at) : '0' }} days</p>
                                            <p class="font-semibold text-gray-900">Duration</p>
                                        </div>
                                    </div>
                                </div>

                               
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2 mt-3">
                               
                               
                                @if ($booking->status->name !== 'Cancelled' && $booking->pickup_at && $booking->pickup_at > now()->addHours(24))
                                    <form action="{{ route('user.booking.cancel', $booking->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        @csrf
                                       
                                        <button type="submit" class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition text-sm font-semibold">
                                            Cancel Booking
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="px-3 py-2 text-gray-400 bg-gray-100 rounded-lg text-sm font-semibold cursor-not-allowed">
                                        Cancel Booking
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <p class="text-gray-500 text-lg">No bookings yet. Start by browsing available cars!</p>
                    <a href="{{ route('user.browse') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Browse Cars
                    </a>
                </div>
            @endempty

            <!-- Pagination -->
            @if ($bookings->hasPages())
                <div class="mt-6">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/user/userdashboard.js') }}"></script>
@endsection