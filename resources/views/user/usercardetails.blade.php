@extends('layouts.userlayout')

@section('content')

  {{-- Flatpickr CDN --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>

  <link rel="stylesheet" href="{{ asset('css/usercardetails.css') }}">

  <div class="flex h-screen bg-white">
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

      <!-- Page Content -->
      <div class="p-8">
        <div class="grid grid-cols-1 gap-6">
          <div class="col-span-1">

            <!-- Image Section -->
            <div class="bg-white rounded-lg mb-6 overflow-hidden">
              <div class="relative h-80 bg-gray-300">
                @if($car->images && count(json_decode($car->images)) > 0)
                  @php $images = json_decode($car->images); @endphp
                  <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $car->model }}"
                       class="w-full h-full object-cover">
                @else
                  <img src="https://images.unsplash.com/photo-1567818735868-e71b99932e29?w=600&h=400&fit=crop" alt="Car"
                       class="w-full h-full object-cover">
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
                <h1 class="text-2xl font-bold text-gray-900 mb-1">
                  {{ $car->brand->name ?? 'N/A' }} {{ $car->model }}
                </h1>
                <div class="flex items-center gap-3">
                  <div class="flex items-center gap-1 text-sm">
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
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
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
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @php
                  $amenities = ['Air Conditioning', 'GPS Navigation', 'USB Charging', 'Leather Seats', 'Bluetooth', 'Sunroof'];
                @endphp
                @foreach($amenities as $a)
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                    </svg>
                    <span class="text-gray-700 text-sm">{{ $a }}</span>
                  </div>
                @endforeach
              </div>
            </div>

            <!-- Rental Terms -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h3 class="font-bold text-gray-900 mb-4 text-sm">Rental Terms & Conditions</h3>

              <div class="space-y-3">
                @php
                  $terms = [
                    'Renter agrees to pay the basic rental fee plus additional charges for excess hours (if any).',
                    'Vehicle must be returned on the agreed return date/time in the same condition (minus normal wear and tear).',
                    'Vehicle must be returned with the agreed fuel expectation (as stated by the owner).',
                    'Vehicle is allowed only within the approved area/location; taking it elsewhere without owner consent has a ₱5,000 penalty.',
                    'Renter must have a valid (legal) driver’s license and declares no outstanding issues against the license.',
                    'Only the renter (and any approved/authorized driver listed) may drive the vehicle.',
                    'Vehicle may be used only for routine, legal purposes (personal or business) and must follow all Philippine laws and rules.',
                    'Renter is responsible for any damages (dents/scratches) and any cleaning fees incurred during the rental period.',
                    'Renter agrees to hold the owner harmless and confirms the vehicle was inspected and accepted in good operating condition.',
                  ];
                @endphp

                @foreach($terms as $t)
                  <div class="flex items-start gap-3">
                    <div class="w-5 h-5 bg-orange-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                      <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
                      </svg>
                    </div>
                    <span class="text-gray-700 text-xs">{{ $t }}</span>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- ✅ BOOKING FORM (STATIC UNDER TERMS) --}}
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
              <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                  <h3 class="text-lg font-bold text-gray-900">Book This Car</h3>
                  <p class="text-xs text-gray-500 mt-1">Fill in the details to continue your booking.</p>
                </div>
                <div class="text-right">
                  <p class="text-xs text-gray-500">Daily rate</p>
                  <p class="text-lg font-bold text-gray-900">₱{{ number_format($car->price_per_day, 0) }}</p>
                </div>
              </div>

              @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                  @foreach ($errors->all() as $error)
                    <p class="text-xs">{{ $error }}</p>
                  @endforeach
                </div>
              @endif

              <form action="{{ route('user.booking.create') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="car_id" value="{{ $car->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Pickup Date</label>
                    <input type="text" id="pickup_date" name="pickup_date" value="{{ request('pickup_date') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-xs" required autocomplete="off">
                  </div>

                  <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Return Date</label>
                    <input type="text" id="return_date" name="return_date" value="{{ request('return_date') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-xs" required autocomplete="off">
                  </div>

                  <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Pickup Time</label>
                    <input type="time" name="pickup_time" value="{{ request('pickup_time') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-xs" required>
                  </div>

                  <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Return Time</label>
                    <input type="time" name="return_time" value="{{ request('return_time') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-xs" required>
                  </div>

                  <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Service Type</label>
                    <select id="service_type_id" name="service_type_id"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-xs" required>
                      <option value="" disabled {{ old('service_type_id') ? '' : 'selected' }}>Select service</option>
                      @foreach($serviceTypes as $st)
                        <option value="{{ $st->id }}" {{ old('service_type_id') == $st->id ? 'selected' : '' }}>
                          {{ $st->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Contact Number</label>
                    <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}"
                           placeholder="09XXXXXXXXX"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-xs" required>
                    <p class="text-[11px] text-gray-500 mt-1">This will be saved to your profile.</p>
                  </div>

                  {{-- Location (toggle) --}}
                  <div id="locationWrap" class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Pickup / Delivery Location</label>
                    <input id="service_location" type="text" name="service_location"
                           value="{{ old('service_location') }}"
                           placeholder="Enter address / landmark"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-xs">
                    <p class="text-[11px] text-gray-500 mt-1">Required for delivery.</p>
                  </div>
                </div>

                <div class="border-t border-gray-200 my-4"></div>

                
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

                  <div class="flex justify-between pt-2 border-t border-gray-200">
                    <span class="text-green-600 font-semibold">Points Discount</span>
                    <span class="text-green-600 font-semibold" id="pointsValueText">-₱0.00</span>
                  </div>
                </div>

                <div class="mb-4">
                  <p class="text-xs text-gray-600 mb-1">Total</p>
                  <input type="hidden" name="total_price" id="total-price-input" value="0">
                  <p class="text-xl font-bold text-gray-900" id="total-price">₱0.00</p>
                </div>

                {{-- Discount Points Card --}}
                @php
                  $availablePoints = (int) (auth()->user()->points_balance ?? 0);
                @endphp

                <div class="border border-gray-200 rounded-lg p-4 mt-4">
                  <h4 class="text-sm font-bold text-gray-900">Discount Points</h4>
                  <p class="text-xs text-gray-600 mt-1">
                    You have <span class="font-semibold">{{ $availablePoints }}</span> points available.
                  </p>

                  <div class="h-px bg-gray-200 my-3"></div>

                  <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-700 whitespace-nowrap">Redeem Points:</label>

                    <input
                      type="number"
                      id="points_to_use"
                      name="points_to_use"
                      min="0"
                      max="{{ $availablePoints }}"
                      value="{{ old('points_to_use', 0) }}"
                      class="flex-1 border border-gray-300 rounded px-3 py-2 text-xs"
                      placeholder="0"
                    >

                    <button type="button" id="applyPointsBtn"
                      class="bg-blue-600 text-white font-semibold px-4 py-2 rounded text-xs hover:bg-blue-700">
                      Apply
                    </button>
                  </div>

                  <p id="pointsError" class="hidden text-xs text-red-600 mt-2"></p>

                  <div class="mt-3 space-y-1 text-xs">
                    <p id="pointsValueText" class="text-green-700 font-semibold hidden"></p>
                    <p id="remainingPointsText" class="text-gray-700 hidden"></p>
                  </div>

                  <input type="hidden" id="points_discount_amount" name="points_discount_amount" value="0">
                </div>

                {{-- Agree to Terms --}}
                <div class="mb-3 mt-4">
                  <label class="flex items-start gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="agree_terms"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-xs text-gray-600">
                      I agree to the <span class="font-semibold text-gray-900">Rental Terms & Conditions</span>.
                    </span>
                  </label>

                  <p id="agreeError" class="hidden text-xs text-red-600 mt-2">
                    Please agree to the Rental Terms & Conditions to continue.
                  </p>
                </div>

                <button type="submit" id="confirmBtn" disabled
                        class="w-full bg-blue-600 text-white font-bold py-2 rounded text-sm transition mb-2
                               disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-700">
                  Confirm Booking
                </button>

                <p class="text-xs text-center text-gray-500">Free cancellation • No hidden fees</p>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    window.BOOKING_CFG = {
      dailyRate: {{ $car->price_per_day }},
      insuranceRate: 0,
      taxRate: 0,
      discountRate: 0,
      unavailableUrl: "{{ route('user.unavailable-dates', $car->id) }}",
      availablePoints: {{ (int) (auth()->user()->points_balance ?? 0) }},
    };
  </script>

  <script src="{{ asset('js/user/usercardetails.js') }}"></script>

@endsection