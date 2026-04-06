@extends('layouts.userlayout')

@section('content')
<div class="min-h-screen bg-gray-50 py-2">
   <div class="max-w-lg mx-auto px-2">

    <div class="bg-white rounded-md border border-gray-200 shadow-sm p-2"></div>
            <h1 class="text-base font-bold text-gray-900 text-center mb-3">
                Return Issue Details
            </h1>

            <p class="text-[11px] text-gray-600 mb-3">
                Booking #{{ $returnIssue->booking->id }} -
                {{ $returnIssue->booking->car->brand->name ?? 'Car' }}
                {{ $returnIssue->booking->car->model ?? '' }}
            </p>

            <div class="space-y-3">

                <div>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between border-b border-gray-100 pb-1.5">
                            <span class="text-gray-500">Issue Type</span>
                            <span class="font-semibold text-gray-900">
                                {{ ucwords(str_replace('_', ' ', $returnIssue->issue_type)) }}
                            </span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-1.5">
                            <span class="text-gray-500">Status</span>
                            <span class="font-semibold text-gray-900">
                                {{ ucfirst($returnIssue->status) }}
                            </span>
                        </div>

                        <div class="flex justify-between border-b border-gray-100 pb-1.5">
                            <span class="text-gray-500">Estimated Charge</span>
                            <span class="font-semibold text-gray-900">
                                ₱{{ number_format($returnIssue->estimated_charge, 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between pb-1">
                            <span class="text-gray-500">Reported At</span>
                            <span class="font-semibold text-gray-900 text-right">
                                {{ optional($returnIssue->reported_at)->format('M d, Y h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xs font-bold text-gray-900 mb-1">
                        {{ $returnIssue->title }}
                    </h2>
                    <p class="text-xs text-gray-700 leading-relaxed">
                        {{ $returnIssue->description ?: 'No additional description provided.' }}
                    </p>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-gray-900 mb-2">Photos</h3>

                    @if($returnIssue->photos->count())
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($returnIssue->photos as $photo)
                                <div class="rounded-md overflow-hidden border border-gray-200">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                         alt="Issue Photo"
                                         class="w-full h-20 object-cover">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-500">No photos uploaded.</p>
                    @endif
                </div>

                <div class="pt-1">
                    <a href="{{ route('user.rentals.index') }}"
                       class="inline-flex items-center justify-center px-3 py-1.5 rounded-md bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition">
                        Back to My Rentals
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection