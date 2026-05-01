@extends('layouts.userlayout')

@section('title', 'My Pending Payments')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $buildCarImageUrl = function ($car) {
        if (!$car) {
            return null;
        }

        $images = [];

        if (is_array($car->images)) {
            $images = $car->images;
        } elseif (is_string($car->images)) {
            $decodedImages = json_decode($car->images, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                $images = $decodedImages;
            } elseif (!empty($car->images)) {
                $images = [$car->images];
            }
        }

        $firstImage = $images[0] ?? null;

        if (!$firstImage) {
            return null;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $firstImage), '/');

        return config('filesystems.default') === 's3'
            ? Storage::disk('s3')->url($cleanPath)
            : asset('storage/' . $cleanPath);
    };
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900">
                        My Pending Payments
                    </h1>

                    <p class="text-slate-500 mt-2 text-base lg:text-lg">
                        Bookings that are waiting for your payment receipt.
                    </p>
                </div>

                <div class="hidden lg:flex h-24 w-28 rounded-3xl bg-orange-100/70 items-center justify-center relative">
                    <div class="h-16 w-20 rounded-xl bg-orange-500 shadow-lg"></div>
                    <div class="absolute -right-3 bottom-3 h-14 w-14 rounded-full bg-white border-4 border-orange-500 flex items-center justify-center">
                        <i class="fas fa-clock text-orange-500 text-xl"></i>
                    </div>
                    <div class="absolute right-2 top-2 h-8 w-8 rounded-full bg-orange-200 flex items-center justify-center">
                        <i class="fas fa-upload text-white text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notice --}}
        <div class="mb-8 rounded-2xl border border-orange-200 bg-orange-50/80 px-5 py-5 lg:px-7">
            <div class="flex gap-4">
                <div class="h-12 w-12 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                    <div class="h-9 w-9 rounded-full bg-orange-500 text-white flex items-center justify-center">
                        <i class="fas fa-info"></i>
                    </div>
                </div>

                <div>
                    <h2 class="font-bold text-slate-900">
                        Pending Payment
                    </h2>

                    <p class="text-sm lg:text-base text-slate-700 mt-1 leading-relaxed">
                        These bookings are saved but not yet paid. Please upload your payment receipt so we can verify and confirm your booking.
                    </p>
                </div>
            </div>
        </div>

        {{-- List --}}
        @if($payments->count() > 0)
            <div class="space-y-5">
                @foreach($payments as $payment)
                    @php
                        $booking = $payment->booking;
                        $car = $booking?->car;

                        $carName = trim(($car?->brand?->name ?? '') . ' ' . ($car?->model ?? ''));
                        $imageUrl = $buildCarImageUrl($car);

                        $transmission = $car?->transmission?->type ?? 'N/A';
                        $fuel = $car?->fuelType?->type ?? 'N/A';
                        $seats = $car?->seats ?? 'N/A';

                        $amount = $payment->amount
                            ?? $booking?->final_total
                            ?? $booking?->total_price
                            ?? 0;
                    @endphp

                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
                        <div class="p-5 lg:p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-[220px_1fr_210px] gap-6 lg:items-center">

                                {{-- Car Image --}}
                                <div class="flex justify-center lg:justify-start">
                                    @if($imageUrl)
                                        <img
                                            src="{{ $imageUrl }}"
                                            alt="{{ $carName }}"
                                            class="w-full max-w-[220px] h-32 object-contain"
                                        >
                                    @else
                                        <div class="w-full max-w-[220px] h-32 rounded-xl bg-slate-100 flex flex-col items-center justify-center text-slate-400">
                                            <i class="fas fa-car text-3xl mb-2"></i>
                                            <span class="text-xs">No Image</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Main Details --}}
                                <div class="min-w-0">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                                        <div>
                                            <h2 class="text-xl lg:text-2xl font-extrabold text-slate-900">
                                                {{ $carName !== '' ? $carName : 'Selected Vehicle' }}
                                            </h2>

                                            <p class="text-slate-500 mt-1">
                                                {{ $transmission }} • {{ $fuel }} • {{ $seats }} Seats
                                            </p>
                                        </div>

                                        <p class="text-sm text-slate-500 lg:text-right">
                                            Booked on {{ optional($payment->created_at)->format('M d, Y') }}
                                        </p>
                                    </div>

                                    <div class="h-px bg-slate-100 my-5"></div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div>
                                            <div class="flex items-center gap-2 text-xs uppercase tracking-wide font-bold text-slate-500 mb-2">
                                                <i class="far fa-calendar-alt text-slate-500"></i>
                                                Pick-up
                                            </div>

                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ optional($booking?->pickup_at)->format('M d, Y') ?? 'N/A' }}
                                            </p>

                                            <p class="text-sm text-slate-700 mt-1">
                                                {{ optional($booking?->pickup_at)->format('h:i A') ?? '' }}
                                            </p>
                                        </div>

                                        <div>
                                            <div class="flex items-center gap-2 text-xs uppercase tracking-wide font-bold text-slate-500 mb-2">
                                                <i class="far fa-calendar-alt text-slate-500"></i>
                                                Return
                                            </div>

                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ optional($booking?->return_at)->format('M d, Y') ?? 'N/A' }}
                                            </p>

                                            <p class="text-sm text-slate-700 mt-1">
                                                {{ optional($booking?->return_at)->format('h:i A') ?? '' }}
                                            </p>
                                        </div>

                                        <div class="sm:border-l sm:border-slate-100 sm:pl-5">
                                            <div class="flex items-center gap-2 text-xs uppercase tracking-wide font-bold text-slate-500 mb-2">
                                                <i class="fas fa-coins text-slate-500"></i>
                                                Total Amount
                                            </div>

                                            <p class="text-lg font-extrabold text-orange-600">
                                                ₱{{ number_format($amount, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action --}}
                                <div class="lg:border-l lg:border-slate-100 lg:pl-6">
                                    <a
                                        href="{{ route('user.payments', ['booking_id' => $booking->id]) }}"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-6 py-3.5 text-white font-bold hover:bg-orange-700 transition shadow-sm"
                                    >
                                        <i class="fas fa-upload"></i>
                                        Pay Now
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-sm text-slate-500">
                    Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} bookings
                </p>

                <div>
                    {{ $payments->links() }}
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-10 text-center">
                <div class="h-20 w-20 mx-auto rounded-full bg-orange-100 text-orange-600 flex items-center justify-center mb-5">
                    <i class="fas fa-wallet text-3xl"></i>
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900">
                    No Pending Payments
                </h2>

                <p class="text-slate-500 mt-2 max-w-md mx-auto">
                    You do not have any saved bookings waiting for payment.
                </p>

                <a
                    href="{{ route('user.browse') }}"
                    class="inline-flex items-center justify-center gap-2 mt-6 px-6 py-3 rounded-xl bg-orange-600 text-white font-bold hover:bg-orange-700 transition"
                >
                    <i class="fas fa-car-side"></i>
                    Browse Cars
                </a>
            </div>
        @endif

    </div>
</div>
@endsection