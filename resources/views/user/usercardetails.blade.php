@extends('layouts.userlayout')

@section('content')

<style>
.red-dot {
    display: block;
    width: 6px;
    height: 6px;
    background: #ef4444;
    border-radius: 50%;
    margin: 0 auto;
    margin-top: 2px;
}

.green-dot {
    display: block;
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    margin: 0 auto;
    margin-top: 2px;
}
</style>

<div class="flex h-screen bg-white">
    <!-- Sidebar -->
    

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Top Navigation -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="px-8 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 text-sm text-gray-600">
                        <a href="{{ route('user.browse') }}" class="hover:text-gray-900">Browse Cars</a>
                        <span>/</span>
                        <span class="text-gray-900 font-semibold">Car Details</span>
                    </div>
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
            </div>
        </div>

        <!-- Content -->
        <div class="p-8">
            <div class="grid grid-cols-4 gap-6">
                <!-- Left Section - Car Details -->
                <div class="col-span-3">
                    <!-- Image Section -->
                    <div class="bg-white rounded-lg mb-6 overflow-hidden">
                        <div class="relative h-80 bg-gray-300">
                            @if($car->images && count(json_decode($car->images)) > 0)
                                @php $images = json_decode($car->images); @endphp
                                <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $car->model }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://images.unsplash.com/photo-1567818735868-e71b99932e29?w=600&h=400&fit=crop" alt="Car" class="w-full h-full object-cover">
                            @endif
                            
                        </div>

                        <!-- Thumbnails -->
                        <div class="p-4 flex gap-2">
                            @if($car->images && count(json_decode($car->images)) > 0)
                                @php $images = json_decode($car->images); @endphp
                                @foreach($images as $index => $image)
                                    <div class="w-24 h-24 rounded-lg overflow-hidden cursor-pointer {{ $index === 0 ? 'border-2 border-blue-500' : 'hover:opacity-80' }}">
                                        <img src="{{ asset('storage/' . $image) }}" alt="Thumb" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-500">No images available</p>
                            @endif
                        </div>
                    </div>

                    <!-- Car Title & Price -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6 flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $car->brand->name ?? 'N/A' }} {{ $car->model }}</h1>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1 text-sm">
                                    <span class="text-yellow-400">★★★★☆</span>
                                    <span class="text-gray-700 font-semibold">4.8 (124 reviews)</span>
                                    <span class="text-gray-500">Downtown Location</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-gray-900">₱{{ number_format($car->price_per_day, 0) }}</p>
                            <p class="text-gray-600 text-sm">/day</p>
                        </div>
                    </div>

                    <!-- Key Features -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <div class="grid grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-3xl mb-2">🧑‍🤝‍🧑</div>
                                <p class="text-xs text-gray-600 font-semibold">Passengers</p>
                                <p class="text-gray-900 font-bold">{{ $car->seats }} People</p>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl mb-2">⚙️</div>
                                <p class="text-xs text-gray-600 font-semibold">Transmission</p>
                                <p class="text-gray-900 font-bold">{{ $car->transmission->type ?? 'N/A' }}</p>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl mb-2">⛽</div>
                                <p class="text-xs text-gray-600 font-semibold">Fuel Type</p>
                                <p class="text-gray-900 font-bold">{{ $car->fuelType->type ?? 'N/A' }}</p>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl mb-2">🧳</div>
                                <p class="text-xs text-gray-600 font-semibold">Luggage</p>
                                <p class="text-gray-900 font-bold">3 Bags</p>
                            </div>
                        </div>
                    </div>

                    <!-- Features & Amenities -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="font-bold text-gray-900 mb-4 text-sm">Features & Amenities</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                </svg>
                                <span class="text-gray-700 text-sm">Air Conditioning</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                </svg>
                                <span class="text-gray-700 text-sm">GPS Navigation</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                </svg>
                                <span class="text-gray-700 text-sm">USB Charging</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                </svg>
                                <span class="text-gray-700 text-sm">Leather Seats</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                </svg>
                                <span class="text-gray-700 text-sm">Bluetooth</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                </svg>
                                <span class="text-gray-700 text-sm">Sunroof</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Terms -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-gray-900 mb-4 text-sm">Rental Terms & Conditions</h3>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-700 text-xs">Minimum age requirement: 25 years old</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-700 text-xs">Valid driver's license required for at least 2 years</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-700 text-xs">Security deposit of ₱500 will be held on your card</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-700 text-xs">Free cancellation up to 24 hours before pickup</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section - Booking Form -->
                <div class="col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-24 h-fit">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Book This Car</h3>

                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                @foreach ($errors->all() as $error)
                                    <p class="text-xs">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('user.booking.create') }}" method="POST" id="bookingForm" onsubmit="handleBookingSubmit(event)">
                            @csrf
                            <input type="hidden" name="car_id" value="{{ $car->id }}">

                            <div class="mb-4">
                                <label class="text-xs font-semibold text-gray-700 mb-1 block">Pickup Date</label>
                                <input type="date" id="pickup_date" name="pickup_date" value="{{ request('pickup_date') }}" class="w-full border border-gray-300 rounded px-2 py-2 text-xs" required>
                            </div>

                            <div class="mb-4">
                                <label class="text-xs font-semibold text-gray-700 mb-1 block">Pickup Time</label>
                                <input type="time" name="pickup_time" value="{{ request('time', ) }}"class="w-full border border-gray-300 rounded px-2 py-2 text-xs" required>
                            </div>

                            <div class="mb-4">
                                <label class="text-xs font-semibold text-gray-700 mb-1 block">Return Date</label>
                               <input type="date" id="return_date" name="return_date" value="{{ request('return_date') }}" class="w-full border border-gray-300 rounded px-2 py-2 text-xs" required>
                            </div>

                            <div class="mb-6">
                                <label class="text-xs font-semibold text-gray-700 mb-1 block">Return Time</label>
                                <input type="time" name="return_time" value="{{ request('time', ) }}"class="w-full border border-gray-300 rounded px-2 py-2 text-xs" required>
                            </div>

                            <div class="border-t border-gray-200 mb-4"></div>

                            <div class="space-y-2 mb-4 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Daily rate</span>
                                    <span class="text-gray-900 font-semibold">₱{{ number_format($car->price_per_day, 0) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Days</span>
                                    <span class="text-gray-900 font-semibold" id="rental-days">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="text-gray-900 font-semibold" id="subtotal">₱0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Insurance</span>
                                    <span class="text-gray-900 font-semibold">₱500</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Taxes & fees</span>
                                    <span class="text-gray-900 font-semibold">₱200</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-green-600 font-semibold">Discount (10%)</span>
                                    <span class="text-green-600 font-semibold" id="discount">-₱0.00</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs text-gray-600 mb-1">Total</p>
                                <input type="hidden" name="total_price" id="total-price-input" value="0">
                                <p class="text-xl font-bold text-gray-900" id="total-price">₱0.00</p>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded text-sm hover:bg-blue-700 transition mb-2">
                                Confirm Booking
                            </button>

                            <p class="text-xs text-center text-gray-500">
                                Free cancellation • No hidden fees
                            </p>
                        </form>
                    </div>
                </div>

                <!-- Modal -->
                <div id="unavailableModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
                        <h2 class="text-lg font-bold text-red-600 mb-2 text-center">
                            Selected Dates Not Available
                        </h2>
                        <p class="text-sm text-gray-600 mb-4 text-center">
                            Some of the selected dates are already booked.
                        </p>
                        
                        <!-- Conflicting Dates Display -->
                        <div id="conflictingDatesContainer" class="mb-4 p-3 bg-red-50 border border-red-200 rounded max-h-40 overflow-y-auto">
                            <p class="text-xs font-semibold text-red-700 mb-2">Booked dates in your selection:</p>
                            <div id="conflictingDatesList" class="text-xs text-red-600 space-y-1">
                                <!-- Conflicting dates will be inserted here -->
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-600 text-center mb-4">
                            Please choose different dates.
                        </p>
                        <button onclick="closeUnavailableModal()"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                            Okay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const dailyRate = {{ $car->price_per_day }};
    const insuranceRate = 500;
    const taxRate = 0.15;
    const discountRate = 0.10;

    function calculatePrice() {
        const pickupDate = new Date(document.querySelector('input[name="pickup_date"]').value);
        const returnDate = new Date(document.querySelector('input[name="return_date"]').value);

        if (!pickupDate || !returnDate || returnDate <= pickupDate) {
            return;
        }

        const timeDiff = returnDate - pickupDate;
        const days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

        const subtotal = dailyRate * days;
        const insurance = insuranceRate;
        const subtotalWithInsurance = subtotal + insurance;
        const taxes = subtotalWithInsurance * taxRate;
        const subtotalBeforeDiscount = subtotalWithInsurance + taxes;
        const discount = subtotalBeforeDiscount * discountRate;
        const total = subtotalBeforeDiscount - discount;

        document.getElementById('rental-days').textContent = days;
        document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
        document.getElementById('discount').textContent = '-₱' + discount.toFixed(2);
        document.getElementById('total-price').textContent = '₱' + total.toFixed(2);
        document.getElementById('total-price-input').value = total.toFixed(2);
    }

    document.querySelector('input[name="pickup_date"]').addEventListener('change', calculatePrice);
    document.querySelector('input[name="return_date"]').addEventListener('change', calculatePrice);

    document.getElementById('toggleSidebar').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('w-56');
        sidebar.classList.toggle('w-0');
    });

    window.addEventListener('load', calculatePrice);

    // Auto-check dates and show modal when unavailable dates are selected
    let unavailableDates = [];

    // Fetch unavailable dates once on page load
    fetch(`{{ route('user.unavailable-dates', $car->id) }}`)
        .then(res => res.json())
        .then(data => {
            unavailableDates = data;
            console.log("Unavailable dates loaded:", unavailableDates);
        })
        .catch(error => console.error('Error fetching unavailable dates:', error));

    // Function to check if dates have conflicts
    function hasConflictInRange(pickup, returnDate) {
        if (!pickup || !returnDate) return false;

        let start = new Date(pickup);
        const end = new Date(returnDate);

        while (start <= end) {
            const formatted = start.toISOString().split('T')[0];
            if (unavailableDates.includes(formatted)) {
                return true;
            }
            start.setDate(start.getDate() + 1);
        }
        return false;
    }

    let lastConflictingRange = null;
    let cachedConflictingDates = null;
    let cachedFullBookedRanges = null;

    // Check dates automatically when either date changes
    function checkDatesAndShowModal() {
        const pickupInput = document.getElementById("pickup_date");
        const returnInput = document.getElementById("return_date");
        const pickup = pickupInput.value;
        const returnDateVal = returnInput.value;

        if (!pickup || !returnDateVal) {
            closeUnavailableModal();
            lastConflictingRange = null;
            cachedConflictingDates = null;
            cachedFullBookedRanges = null;
            return;
        }

        if (hasConflictInRange(pickup, returnDateVal)) {
            const currentRange = pickup + "_" + returnDateVal;
            
            if (currentRange !== lastConflictingRange) {
                lastConflictingRange = currentRange;
                
                // Fetch and find complete booked ranges
                fetch(`{{ route('user.unavailable-dates', $car->id) }}`)
                    .then(res => res.json())
                    .then(unavailableDatesList => {
                        const conflictingDates = [];
                        let checkDate = new Date(pickup);
                        const endDate = new Date(returnDateVal);

                        while (checkDate <= endDate) {
                            const formatted = checkDate.toISOString().split('T')[0];
                            if (unavailableDatesList.includes(formatted)) {
                                conflictingDates.push(formatted);
                            }
                            checkDate.setDate(checkDate.getDate() + 1);
                        }

                        const fullBookedRanges = findCompleteBookedRanges(unavailableDatesList, conflictingDates);
                        
                        cachedConflictingDates = conflictingDates;
                        cachedFullBookedRanges = fullBookedRanges;
                        
                        showUnavailableModal();
                        displayCompleteBookedRanges();
                    });
            }
        } else {
            closeUnavailableModal();
            lastConflictingRange = null;
            cachedConflictingDates = null;
            cachedFullBookedRanges = null;
        }
    }

    // Find complete booked period that contains the conflicting dates
    function findCompleteBookedRanges(allUnavailableDates, conflictingDates) {
        if (conflictingDates.length === 0) return [];

        const sortedDates = allUnavailableDates
            .map(dateStr => new Date(dateStr + 'T00:00:00'))
            .sort((a, b) => a - b);

        const ranges = [];
        let rangeStart = new Date(sortedDates[0]);
        let rangeEnd = new Date(sortedDates[0]);

        for (let i = 1; i < sortedDates.length; i++) {
            const currentDate = new Date(sortedDates[i]);
            const prevDate = new Date(sortedDates[i - 1]);
            
            const diffTime = currentDate - prevDate;
            const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays === 1) {
                rangeEnd = new Date(currentDate);
            } else {
                ranges.push({ start: rangeStart, end: rangeEnd });
                rangeStart = new Date(currentDate);
                rangeEnd = new Date(currentDate);
            }
        }
        ranges.push({ start: rangeStart, end: rangeEnd });

        const conflictingDateObjects = conflictingDates.map(d => new Date(d + 'T00:00:00'));
        return ranges.filter(range => {
            return conflictingDateObjects.some(conflictDate => 
                conflictDate >= range.start && conflictDate <= range.end
            );
        });
    }

    // Check dates when pickup date changes
    document.getElementById("pickup_date").addEventListener("change", function () {
        checkDatesAndShowModal();
        calculatePrice();
    });

    // Check dates when return date changes
    document.getElementById("return_date").addEventListener("change", function () {
        checkDatesAndShowModal();
        calculatePrice();
        
        const returnTimeInput = document.querySelector('input[name="return_time"]');
        const pickupTimeInput = document.querySelector('input[name="pickup_time"]');
        
        if (returnTimeInput.value === '') {
            if (pickupTimeInput.value) {
                returnTimeInput.value = pickupTimeInput.value;
            } else {
                returnTimeInput.value = '10:00';
            }
        }
    });

    function handleBookingSubmit(e) {
        e.preventDefault();
        
        const pickupInput = document.getElementById("pickup_date");
        const returnInput = document.getElementById("return_date");
        const pickup = pickupInput.value;
        const returnDateVal = returnInput.value;

        if (!pickup || !returnDateVal) {
            alert('Please select both pickup and return dates');
            return;
        }

        fetch(`{{ route('user.unavailable-dates', $car->id) }}`)
            .then(res => res.json())
            .then(unavailableDatesList => {
                let start = new Date(pickup);
                const end = new Date(returnDateVal);
                let hasConflict = false;

                while (start <= end) {
                    const formatted = start.toISOString().split('T')[0];
                    if (unavailableDatesList.includes(formatted)) {
                        hasConflict = true;
                        break;
                    }
                    start.setDate(start.getDate() + 1);
                }

                if (hasConflict) {
                    showUnavailableModal();
                    return;
                }

                submitBookingForm();
            })
            .catch(error => {
                console.error('Error checking dates:', error);
                submitBookingForm();
            });
    }

    function submitBookingForm() {
        const form = document.getElementById("bookingForm");
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else if (data.error_type === 'unavailable_dates') {
                showUnavailableModal();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Booking error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function showUnavailableModal() {
        const modal = document.getElementById("unavailableModal");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    }

    function closeUnavailableModal() {
        const modal = document.getElementById("unavailableModal");
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }

    function displayCompleteBookedRanges() {
        if (!cachedFullBookedRanges || cachedFullBookedRanges.length === 0) {
            console.log("No complete booked ranges to display");
            return;
        }

        const datesList = document.getElementById("conflictingDatesList");
        datesList.innerHTML = '';

        cachedFullBookedRanges.forEach(range => {
            const startFormatted = range.start.toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            
            const endFormatted = range.end.toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            
            const dateItem = document.createElement('div');
            dateItem.className = 'flex items-center gap-2 text-xs';
            
            if (startFormatted === endFormatted) {
                dateItem.innerHTML = `
                    <span class="text-red-600">●</span>
                    <span>${startFormatted}</span>
                `;
            } else {
                dateItem.innerHTML = `
                    <span class="text-red-600">●</span>
                    <span>${startFormatted} to ${endFormatted}</span>
                `;
            }
            
            datesList.appendChild(dateItem);
        });
    }

</script>
@endsection