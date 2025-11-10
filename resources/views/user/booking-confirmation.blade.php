@extends('layouts.userlayout')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-2xl mx-auto px-6">
        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <div>
                    <h2 class="text-2xl font-bold text-green-900">Payment Successful!</h2>
                    <p class="text-green-700">Your booking has been confirmed</p>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Booking Details</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-700">Booking ID:</span>
                    <span class="font-semibold">#{{ $booking->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Car:</span>
                    <span class="font-semibold">{{ $booking->car->brand->name }} {{ $booking->car->model }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Pickup Date:</span>
                    <span class="font-semibold">{{ $booking->pickup_at }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Return Date:</span>
                    <span class="font-semibold">{{ $booking->return_at }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        @if($payment)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Payment Details</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-700">Amount:</span>
                    <span class="text-2xl font-bold text-green-600">₱{{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Payment Method:</span>
                    <span class="font-semibold">{{ $payment->paymentMethod->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Status:</span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold">{{ $payment->paymentStatus->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Transaction ID:</span>
                    <span class="font-mono text-sm">{{ $payment->transaction_id }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Receipt -->
        @if($receipt)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Receipt</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-700">Receipt Number:</span>
                    <span class="font-semibold">{{ $receipt->receipt_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Generated:</span>
                    <span class="font-semibold">{{ $receipt->generated_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="{{ route('user.rentals') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg text-center">
                View My Rentals
            </a>
            <a href="{{ route('user.browse') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-lg text-center">
                Browse More Cars
            </a>
        </div>
    </div>
</div>
@endsection