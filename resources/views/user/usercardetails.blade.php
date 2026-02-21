@extends('layouts.userlayout')

@section('content')

{{-- Flatpickr CDN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>

<style>
  /* Dots */
  .flatpickr-day { position: relative; }
  .red-dot, .green-dot{
    position:absolute;
    bottom:3px;
    left:50%;
    transform:translateX(-50%);
    width:7px;height:7px;
    border-radius:999px;
  }
  .red-dot{ background:#ef4444; }
  .green-dot{ background:#22c55e; }

  /* Legend */
  .fp-legend{
    display:flex;
    gap:20px;
    align-items:center;
    padding:10px 12px;
    border-bottom:1px solid #e5e7eb;
    font-size:12px;
  }
  .fp-legend .item{ display:flex; align-items:center; gap:8px; color:#111827; }
  .fp-legend .dot{ width:10px; height:10px; border-radius:999px; }
  .fp-legend .dot.red{ background:#ef4444; }
  .fp-legend .dot.green{ background:#22c55e; }

  /* ===== WEB centered calendar + overlay ===== */
  .flatpickr-calendar.fp-center{
    position:fixed !important;
    top:50% !important;
    left:50% !important;
    right:auto !important;
    transform:translate(-50%,-50%) !important;
    z-index:99999 !important;
    border-radius:14px;
    box-shadow:0 12px 40px rgba(0,0,0,.25);
  }
  .fp-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.35);
    z-index:99998;
  }

  /* Optional: mobile sizing (still centered) */
  @media (max-width: 640px){
    .flatpickr-calendar.fp-center{
      width:calc(100% - 24px) !important;
      max-width:360px !important;
    }
    .flatpickr-rContainer,
    .flatpickr-days,
    .dayContainer{
      width:100% !important;
      min-width:100% !important;
      max-width:100% !important;
    }
    .flatpickr-day{
      height:44px !important;
      line-height:44px !important;
      max-width:44px !important;
    }
    .red-dot,.green-dot{ width:8px; height:8px; bottom:5px; }
    .fp-legend{ justify-content:center; gap:16px; padding:12px; }
  }
</style>

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
              @php
                $amenities = ['Air Conditioning','GPS Navigation','USB Charging','Leather Seats','Bluetooth','Sunroof'];
              @endphp
              @foreach($amenities as $a)
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
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
                <input type="text" id="pickup_date" name="pickup_date" value="{{ request('pickup_date') }}"
                       class="w-full border border-gray-300 rounded px-2 py-2 text-xs" required autocomplete="off">
              </div>

              <div class="mb-4">
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Pickup Time</label>
                <input type="time" name="pickup_time" value="{{ request('pickup_time') }}"
                       class="w-full border border-gray-300 rounded px-2 py-2 text-xs" required>
              </div>

              <div class="mb-4">
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Return Date</label>
                <input type="text" id="return_date" name="return_date" value="{{ request('return_date') }}"
                       class="w-full border border-gray-300 rounded px-2 py-2 text-xs" required autocomplete="off">
              </div>

              <div class="mb-6">
                <label class="text-xs font-semibold text-gray-700 mb-1 block">Return Time</label>
                <input type="time" name="return_time" value="{{ request('return_time') }}"
                       class="w-full border border-gray-300 rounded px-2 py-2 text-xs" required>
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

              <p class="text-xs text-center text-gray-500">Free cancellation • No hidden fees</p>
            </form>
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

  let unavailableSet = new Set(); // ✅ REQUIRED

  function ymd(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }

  function calculatePrice() {
    const pickupVal = document.querySelector('input[name="pickup_date"]').value;
    const returnVal = document.querySelector('input[name="return_date"]').value;
    if (!pickupVal || !returnVal) return;

    const pickupDate = new Date(pickupVal + "T00:00:00");
    const returnDate = new Date(returnVal + "T00:00:00");
    if (returnDate <= pickupDate) return;

    const days = Math.ceil((returnDate - pickupDate) / (1000*60*60*24));
    const subtotal = dailyRate * days;
    const subtotalWithInsurance = subtotal + insuranceRate;
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

  // ✅ Init Flatpickr (with dots + legend + rangePlugin)
  function initDatePicker() {
    const input = document.getElementById("pickup_date");
    if (!input) return;

    // prevent double init
    if (input._flatpickr) input._flatpickr.destroy();

    flatpickr("#pickup_date", {
      dateFormat: "Y-m-d",
      minDate: "today",
      disableMobile: true,
      showMonths: 1,
      appendTo: document.body,
      plugins: [ new rangePlugin({ input: "#return_date" }) ],

      disable: [
        (date) => unavailableSet.has(ymd(date))
      ],

      onReady: (selectedDates, dateStr, fp) => {
        // legend
        const legend = document.createElement("div");
        legend.className = "fp-legend";
        legend.innerHTML = `
          <div class="item"><span class="dot red"></span> Unavailable</div>
          <div class="item"><span class="dot green"></span> Available</div>
        `;
        fp.calendarContainer.prepend(legend);

        // center + overlay (optional)
        fp.calendarContainer.classList.add("fp-center");
      },

      onOpen: (selectedDates, dateStr, fp) => {
        fp.calendarContainer.classList.add("fp-center");

        if (!document.querySelector(".fp-overlay")) {
          const overlay = document.createElement("div");
          overlay.className = "fp-overlay";
          overlay.addEventListener("click", () => fp.close());
          document.body.appendChild(overlay);
        }
      },

      onClose: () => {
        const overlay = document.querySelector(".fp-overlay");
        if (overlay) overlay.remove();
      },

      onDayCreate: (dObj, dStr, fp, dayElem) => {
        const key = ymd(dayElem.dateObj);
        const dot = document.createElement("span");
        dot.className = unavailableSet.has(key) ? "red-dot" : "green-dot";
        dayElem.appendChild(dot);
      },

      onChange: () => {
        calculatePrice();
      }
    });
  }

  // ✅ Sidebar toggle (correct for your -translate-x-full sidebar)
  function initSidebarToggle() {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if (!toggleBtn || !sidebar) return;

    const open = () => {
      sidebar.classList.remove("-translate-x-full");
      sidebar.classList.add("translate-x-0");
      if (overlay) overlay.classList.remove("hidden");
    };

    const close = () => {
      sidebar.classList.add("-translate-x-full");
      sidebar.classList.remove("translate-x-0");
      if (overlay) overlay.classList.add("hidden");
    };

    const isOpen = () => !sidebar.classList.contains("-translate-x-full");

    toggleBtn.addEventListener("click", () => {
      isOpen() ? close() : open();
    });

    if (overlay) overlay.addEventListener("click", close);
  }

  // ✅ Load unavailable dates then init picker
  function loadUnavailableDates() {
    fetch(`{{ route('user.unavailable-dates', $car->id) }}`)
      .then(res => res.json())
      .then(data => {
        unavailableSet = new Set(data || []);
      })
      .catch(err => {
        console.error("Failed to load unavailable dates:", err);
        unavailableSet = new Set();
      })
      .finally(() => {
        initDatePicker();
      });
  }

  // ✅ Submit validation (no modal, just block)
  function handleBookingSubmit(e) {
    e.preventDefault();

    const pickup = document.getElementById("pickup_date").value;
    const ret = document.getElementById("return_date").value;
    if (!pickup || !ret) return alert("Please select both pickup and return dates.");

    let start = new Date(pickup + "T00:00:00");
    const end = new Date(ret + "T00:00:00");

    while (start <= end) {
      if (unavailableSet.has(ymd(start))) {
        alert("Selected dates include unavailable days. Please choose different dates.");
        return;
      }
      start.setDate(start.getDate() + 1);
    }

    document.getElementById("bookingForm").submit();
  }

  // Make handleBookingSubmit available globally (for onsubmit)
  window.handleBookingSubmit = handleBookingSubmit;

  document.addEventListener("DOMContentLoaded", () => {
    initSidebarToggle();
    loadUnavailableDates();
    calculatePrice();
  });
</script>


@endsection
