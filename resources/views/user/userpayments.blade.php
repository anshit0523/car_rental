@extends('layouts.userlayout')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="px-6 py-4 flex items-center">
                <a href="{{ route('user.browse') }}" class="text-gray-700 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 ml-4">Payment</h1>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-2xl mx-auto px-6 py-8">
            <!-- Booking Summary Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-start gap-4">
                    <!-- Car Image -->
                    <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-300 flex-shrink-0">
                        @if($booking->car->images && count(json_decode($booking->car->images)) > 0)
                            @php $images = json_decode($booking->car->images); @endphp
                            <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $booking->car->model }}"
                                class="w-full h-full object-cover">
                        @else
                            <img src="https://images.unsplash.com/photo-1567818735868-e71b99932e29?w=200&h=200&fit=crop"
                                alt="Car" class="w-full h-full object-cover">
                        @endif
                    </div>

                    <!-- Booking Details -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $booking->car->brand->name ?? 'N/A' }}
                                    {{ $booking->car->model }}
                                </h3>

                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">
                                    ₱{{ number_format($booking->car->price_per_day, 0) }}/day</p>
                                <p class="text-gray-600 text-xs">
                                    @php
                                        $pickup = \Carbon\Carbon::parse($booking->pickup_at)->startOfDay();
                                        $return = \Carbon\Carbon::parse($booking->return_at)->startOfDay();
                                    @endphp

                                <p class="text-gray-600 text-xs">
                                    {{ $pickup->diffInDays($return) }} days
                                </p>
                                </p>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2 text-gray-700">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z" />
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($booking->pickup_at)->format('M d') }} -
                                    {{ \Carbon\Carbon::parse($booking->return_at)->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
                                </svg>
                                <span>Downtown Location</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Price Breakdown</h2>

                @php
                    $rentalDays = max(
                        1,
                        \Carbon\Carbon::parse($booking->pickup_at)->diffInDays(\Carbon\Carbon::parse($booking->return_at))
                    );

                    $rentalCost = $booking->car->price_per_day * $rentalDays;

                    // removed extras
                    $subtotal = $rentalCost;

                    // points discount stored on booking
                    $pointsDiscount = (float) ($booking->discount_amount ?? 0);

                    // final total (use stored final_total if available, otherwise compute)
                    $total = $booking->final_total !== null
                        ? (float) $booking->final_total
                        : max(0, $subtotal - $pointsDiscount);
                @endphp

                <div class="space-y-3 mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Rental ({{ (int) $rentalDays }} days)</span>
                        <span class="text-gray-900 font-semibold">₱{{ number_format($rentalCost, 0) }}</span>
                    </div>


                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount Points</span>
                        <span class="text-gray-900 font-semibold">
                            {{ (int) ($booking->points_used ?? 0) }} points =
                            ₱{{ number_format((float) ($booking->discount_amount ?? 0), 2) }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-3 flex items-center justify-between">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="text-2xl font-bold text-gray-900">₱{{ number_format($total, 2) }}</span>
                </div>
            </div>

            <!-- Payment Method Selection -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Method</h2>

                <div class="space-y-3">
                    <!-- Credit/Debit Card -->
                    <label
                        class="payment-method-label flex items-center p-4 border-2 border-blue-600 rounded-lg cursor-pointer bg-blue-50"
                        data-method="card">
                        <input type="radio" name="payment_method" value="card" checked class="w-4 h-4 text-blue-600">
                        <div class="ml-3 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20 8H4V6h16m0 10H4v-4h16m0 6H4v-2h16m2-10v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2z" />
                            </svg>
                            <span class="font-semibold text-gray-900">Credit/Debit Card</span>
                        </div>
                    </label>

                    <!-- PayPal -->
                    <label
                        class="payment-method-label flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300"
                        data-method="paypal">
                        <input type="radio" name="payment_method" value="paypal" class="w-4 h-4 text-blue-600">
                        <div class="ml-3 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M9.67 12c0 .5-.41.9-.91.9H7v-1.8h1.76c.5 0 .91.4.91.9zm3.9-2.3h-2.13V9h2.05c.47 0 .85.38.85.85 0 .47-.38.85-.77.85z" />
                            </svg>
                            <span class="font-semibold text-gray-900">PayPal</span>
                        </div>
                    </label>

                    <!-- Apple Pay -->
                    <label
                        class="payment-method-label flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300"
                        data-method="apple_pay">
                        <input type="radio" name="payment_method" value="apple_pay" class="w-4 h-4 text-blue-600">
                        <div class="ml-3 flex items-center gap-2">
                            <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.05 13.5c-.91 0-1.64.54-2.05 1.27h-.08c-.4-.75-1.07-1.27-2.05-1.27-1.27 0-2.32 1.24-2.32 2.53 0 1.28 1.05 2.53 2.32 2.53.98 0 1.65-.52 2.05-1.27h.08c.41.75 1.14 1.27 2.05 1.27 1.27 0 2.32-1.24 2.32-2.53 0-1.29-1.05-2.53-2.32-2.53zM9.5 3h5v2h-5z" />
                            </svg>
                            <span class="font-semibold text-gray-900">Apple Pay</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Card Details Form (shown only for card payment) -->
            <form action="{{ route('user.payment.process') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="payment_method" id="paymentMethod" value="card">

                <div id="cardDetailsSection" class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Card Details</h2>

                    <!-- Card Number -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Card Number</label>
                        <div class="relative">
                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                            <img src="https://www.visa.com/favicon.ico" alt="Visa" class="absolute right-3 top-2.5 w-6 h-6">
                        </div>
                    </div>

                    <!-- Expiry and CVV -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Expiry Date</label>
                            <input type="text" name="expiry_date" placeholder="MM/YY" maxlength="5"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">CVV</label>
                            <input type="text" name="cvv" placeholder="123" maxlength="4"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                        </div>
                    </div>

                    <!-- Cardholder Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cardholder Name</label>
                        <input type="text" name="cardholder_name" placeholder="John Doe"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2" value="{{ auth()->user()->name }}"
                            required>
                    </div>

                    <!-- Billing Address -->
                    <h3 class="font-bold text-gray-900 mb-3 mt-6">Billing Address</h3>

                    <div class="mb-4 flex items-center gap-2">
                        <input type="checkbox" id="sameAsPickup" name="same_as_pickup" checked class="w-4 h-4 rounded">
                        <label for="sameAsPickup" class="text-sm font-semibold text-gray-700">Same as pickup</label>
                    </div>



                    <!-- Security Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-blue-900 text-sm">Secure Payment</p>
                            <p class="text-blue-700 text-xs">Your payment information is encrypted and secure. We never
                                store your card details.</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18 8h-1V6c0-2.76-2.24-5-5-5s-5 2.24-5 5v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
                        </svg>
                        Pay ₱{{ number_format($total, 2) }}
                    </button>
                </div>
            </form>

            <!-- PayPal Redirect (shown when PayPal is selected) -->
            <div id="paypalSection" class="bg-white rounded-lg shadow-md p-6 mb-6 hidden">
                <div class="text-center">
                    <svg class="w-16 h-16 text-blue-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M9.67 12c0 .5-.41.9-.91.9H7v-1.8h1.76c.5 0 .91.4.91.9zm3.9-2.3h-2.13V9h2.05c.47 0 .85.38.85.85 0 .47-.38.85-.77.85z" />
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">PayPal Payment</h3>
                    <p class="text-gray-600 mb-6">You will be redirected to PayPal to complete your payment securely.</p>
                    <button onclick="redirectToPayPal()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition">
                        Continue to PayPal
                    </button>
                </div>
            </div>

            <!-- Apple Pay (shown when Apple Pay is selected) -->
            <div id="applepaySection" class="bg-white rounded-lg shadow-md p-6 mb-6 hidden">
                <div class="text-center">
                    <svg class="w-16 h-16 text-black mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.05 13.5c-.91 0-1.64.54-2.05 1.27h-.08c-.4-.75-1.07-1.27-2.05-1.27-1.27 0-2.32 1.24-2.32 2.53 0 1.28 1.05 2.53 2.32 2.53.98 0 1.65-.52 2.05-1.27h.08c.41.75 1.14 1.27 2.05 1.27 1.27 0 2.32-1.24 2.32-2.53 0-1.29-1.05-2.53-2.32-2.53zM9.5 3h5v2h-5z" />
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Apple Pay</h3>
                    <p class="text-gray-600 mb-6">Complete your payment using Apple Pay on your device.</p>
                    <button onclick="redirectToApplePay()"
                        class="w-full bg-black hover:bg-gray-900 text-white font-bold py-3 rounded-lg transition">
                        Pay with Apple Pay
                    </button>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        const cardDetailsSection = document.getElementById('cardDetailsSection');
        const paypalSection = document.getElementById('paypalSection');
        const applepaySection = document.getElementById('applepaySection');
        const paymentMethodLabels = document.querySelectorAll('.payment-method-label');
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
        const paymentMethodInput = document.getElementById('paymentMethod');

        // Initialize visibility
        function updatePaymentMethodUI() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

            // Reset all sections
            cardDetailsSection.classList.add('hidden');
            paypalSection.classList.add('hidden');
            applepaySection.classList.add('hidden');

            // Reset all labels
            paymentMethodLabels.forEach(label => {
                label.classList.remove('border-blue-600', 'bg-blue-50');
                label.classList.add('border-gray-200', 'hover:border-gray-300');
            });

            // Update active label
            const activeLabel = document.querySelector(`.payment-method-label[data-method="${selectedMethod}"]`);
            if (activeLabel) {
                activeLabel.classList.remove('border-gray-200', 'hover:border-gray-300');
                activeLabel.classList.add('border-blue-600', 'bg-blue-50');
            }

            // Show appropriate section
            if (selectedMethod === 'card') {
                cardDetailsSection.classList.remove('hidden');
            } else if (selectedMethod === 'paypal') {
                paypalSection.classList.remove('hidden');
            } else if (selectedMethod === 'apple_pay') {
                applepaySection.classList.remove('hidden');
            }

            // Update hidden input
            paymentMethodInput.value = selectedMethod;
        }

        // Event listeners for payment method change
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', updatePaymentMethodUI);
        });

        // Format card number
        const cardNumberInput = document.querySelector('input[name="card_number"]');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function (e) {
                const v = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                const matches = v.match(/\d{4,16}/g);
                const match = (matches && matches[0]) || '';
                const parts = [];
                for (let i = 0, len = match.length; i < len; i += 4) {
                    parts.push(match.substring(i, i + 4));
                }
                if (parts.length) {
                    e.target.value = parts.join(' ');
                } else {
                    e.target.value = v;
                }
            });
        }

        // Format expiry date
        const expiryInput = document.querySelector('input[name="expiry_date"]');
        if (expiryInput) {
            expiryInput.addEventListener('input', function (e) {
                const v = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                if (v.length >= 2) {
                    e.target.value = v.slice(0, 2) + '/' + v.slice(2, 4);
                } else {
                    e.target.value = v;
                }
            });
        }

        // PayPal redirect function
        function redirectToPayPal() {
            const bookingId = document.querySelector('input[name="booking_id"]').value;
            window.location.href = `/user/paypal/payment/${bookingId}`;
        }

        // Apple Pay redirect function
        function redirectToApplePay() {
            // Submit form for Apple Pay processing
            document.getElementById('paymentForm').submit();
        }

        // Initialize on page load
        updatePaymentMethodUI();
    </script>
@endsection