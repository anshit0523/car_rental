@extends('layouts.userlayout')

@section('content')

    <div class="min-h-screen bg-gray-50">

        <!-- HEADER -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="px-4 sm:px-6 py-4 flex items-center">
                <a href="{{ route('user.browse') }}" class="text-gray-700 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 ml-4">
                    Payment
                </h1>
            </div>
        </div>

        @php

            $rentalDays = max(
                1,
                \Carbon\Carbon::parse($booking->pickup_at)
                    ->diffInDays(\Carbon\Carbon::parse($booking->return_at))
            );

            $rentalCost = $booking->car->price_per_day * $rentalDays;

            $pointsDiscount = (float) ($booking->discount_amount ?? 0);

            $total = $booking->final_total !== null
                ? (float) $booking->final_total
                : max(0, $rentalCost - $pointsDiscount);

            $images = $booking->car->images ? json_decode($booking->car->images) : [];

        @endphp


        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- LEFT SIDE -->
                <div class="lg:col-span-8 space-y-6 order-2 lg:order-1">

                    <!-- PAYMENT METHOD -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">

                        <h2 class="text-lg font-bold mb-4">
                            Select Payment Method
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <label
                                class="payment-option border-2 border-gray-200 rounded-lg p-4 flex items-center gap-3 cursor-pointer"
                                data-method="gcash">

                                <input type="radio" name="payment_method" class="hidden">

                                <span class="text-xl">💙</span>

                                <span class="font-semibold">
                                    GCash
                                </span>

                            </label>


                            <label
                                class="payment-option border-2 border-gray-200 rounded-lg p-4 flex items-center gap-3 cursor-pointer"
                                data-method="bank">

                                <input type="radio" name="payment_method" class="hidden">

                                <span class="text-xl">🏦</span>

                                <span class="font-semibold">
                                    Bank Transfer
                                </span>

                            </label>

                        </div>

                    </div>


                    <form action="{{ route('user.payment.process') }}" method="POST" enctype="multipart/form-data">

                        @csrf

                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <input type="hidden" name="payment_method" id="paymentMethod">

                        <!-- GCASH SECTION -->

                        <div id="gcashSection" class="bg-white rounded-xl border border-gray-200 p-6 hidden">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">

                                <!-- LEFT -->
                                <div>

                                    <div class="flex items-center gap-3 mb-2">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                                            G
                                        </div>

                                        <h3 class="text-lg font-semibold text-gray-900">
                                            GCash QR Payment
                                        </h3>
                                    </div>

                                    <p class="text-sm text-gray-500 mb-6">
                                        Scan the QR code using your GCash app.
                                    </p>

                                    <div class="border-t border-gray-200 my-4"></div>

                                    <div class="flex items-center gap-2 mb-6">
                                        <span class="text-green-600 text-xl">🏦</span>

                                        <span class="font-semibold text-gray-800">
                                            Car Rental PH
                                        </span>
                                    </div>

                                    <h4 class="font-semibold text-gray-800 mb-1">
                                        Upload Payment Receipt
                                    </h4>

                                    <p class="text-sm text-gray-500 mb-4">
                                        Upload a screenshot of your payment.
                                    </p>


                                    <label
                                        class="border border-gray-300 rounded-lg p-4 flex items-center justify-between cursor-pointer hover:border-gray-400 transition">

                                        <div>
                                            <p class="text-sm font-medium text-gray-800">
                                                Upload Payment Screenshot
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                JPG, PNG or JPEG
                                            </p>
                                        </div>

                                        <span class="text-sm text-blue-600 font-medium">
                                            Browse
                                        </span>

                                        <input type="file" name="receipt_image" accept="image/*" class="hidden">

                                    </label>

                                </div>


                                <!-- RIGHT -->
                                <div class="text-center">

                                    <img src="{{ asset('storage/cars/gcashqr.jpg') }}"
                                        class="w-44 sm:w-52 mx-auto mb-3 border rounded-lg p-2 bg-white">

                                    <p class="font-semibold text-gray-800">
                                        Car Rental PH
                                    </p>

                                    <p class="text-sm text-gray-500">
                                        GCash Number: 0912-345-6789
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- BANK SECTION -->

                        <div id="bankSection" class="bg-white rounded-xl shadow-sm border p-6 hidden">

                            <h3 class="text-lg font-bold mb-4">
                                Bank Transfer
                            </h3>

                            <p class="text-gray-500 text-sm mb-4">
                                Send payment to the bank account below.
                            </p>

                            <div class="bg-gray-50 rounded-lg p-5 mb-5">

                                <div class="flex justify-between mb-3">
                                    <span class="text-gray-500">Bank</span>
                                    <span class="font-semibold">BDO</span>
                                </div>

                                <div class="flex justify-between mb-3">
                                    <span class="text-gray-500">Account Name</span>
                                    <span class="font-semibold">Car Rental PH</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-500">Account Number</span>
                                    <span class="font-semibold tracking-wider">
                                        1234 5678 9012
                                    </span>
                                </div>

                            </div>

                            <label class="font-semibold mb-2 block">
                                Upload Bank Receipt
                            </label>

                            <input type="file" name="bank_receipt" accept="image/*"
                                class="border rounded-lg px-4 py-2 w-full">

                            <p class="text-xs text-gray-500 mt-2">
                                Upload screenshot after sending bank transfer.
                            </p>

                        </div>


                        <button type="submit"
                            class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg">

                            Submit Payment Proof

                        </button>

                    </form>

                </div>


                <!-- RIGHT SIDE SUMMARY -->

                <div class="lg:col-span-4 order-1 lg:order-2">

                    <div class="bg-white rounded-xl shadow-sm border p-6 lg:sticky lg:top-24">

                        <h2 class="text-lg font-bold mb-4">
                            Booking Summary
                        </h2>

                        <div class="flex gap-4 mb-5">

                            @if($images && count($images) > 0)

                                <img src="{{ asset('storage/' . $images[0]) }}" class="w-20 h-20 rounded-lg object-cover">

                            @endif

                            <div>

                                <p class="font-semibold">
                                    {{ $booking->car->brand->name ?? 'N/A' }}
                                    {{ $booking->car->model }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($booking->pickup_at)->format('M d') }}
                                    -
                                    {{ \Carbon\Carbon::parse($booking->return_at)->format('M d, Y') }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    {{ $rentalDays }} days rental
                                </p>

                            </div>

                        </div>

                        <div class="border-t pt-4 space-y-2 text-sm">

                            <div class="flex justify-between">
                                <span>Rental</span>
                                <span>₱{{ number_format($rentalCost, 0) }}</span>
                            </div>

                            <div class="flex justify-between text-gray-500">
                                <span>Discount Points</span>
                                <span>-₱{{ number_format($pointsDiscount, 2) }}</span>
                            </div>

                            <div class="border-t pt-3 flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>₱{{ number_format($total, 2) }}</span>
                            </div>

                        </div>

                      

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script>

        const paymentOptions = document.querySelectorAll('.payment-option');
        const gcashSection = document.getElementById('gcashSection');
        const bankSection = document.getElementById('bankSection');
        const paymentMethodInput = document.getElementById('paymentMethod');

        paymentOptions.forEach(option => {

            option.addEventListener('click', () => {

                const method = option.dataset.method;

                gcashSection.classList.add('hidden');
                bankSection.classList.add('hidden');

                paymentOptions.forEach(o => {
                    o.classList.remove('border-blue-500', 'bg-blue-50');
                });

                option.classList.add('border-blue-500', 'bg-blue-50');

                paymentMethodInput.value = method;

                if (method === 'gcash') gcashSection.classList.remove('hidden');
                if (method === 'bank') bankSection.classList.remove('hidden');

            });

        });

    </script>

@endsection