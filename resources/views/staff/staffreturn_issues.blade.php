@extends('layouts.adminlayout')

@section('content')
<div class="flex h-screen overflow-hidden">
    <div class="flex-1 overflow-y-auto bg-gray-100 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Return Issue</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Record a damage report or return-related issue for this booking.
                    </p>
                </div>

                <a href="{{ route('staff.bookings.index') }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                    Back to Bookings
                </a>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-xl bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                    <p class="font-semibold mb-2">Please fix the following:</p>
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $selectedUpdate = $prefillStatus ?? 'N/A';

                $selectedUpdateClass = match($selectedUpdate) {
                    'Damage' => 'bg-red-100 text-red-800 border-red-200',
                    'Checkup' => 'bg-orange-100 text-orange-800 border-orange-200',
                    'Needs Repair' => 'bg-purple-100 text-purple-800 border-purple-200',
                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                };
            @endphp

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">Booking Summary</h2>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Booking ID</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">#{{ $booking->id }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Customer</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $booking->user->name ?? 'N/A' }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Vehicle</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ $booking->car->brand->name ?? 'Car' }} {{ $booking->car->model ?? '' }}
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Current Booking Status</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $booking->status->name ?? 'N/A' }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Selected Update</p>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $selectedUpdateClass }}">
                                        {{ $selectedUpdate }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Pick-up</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ $booking->pickup_at ? \Carbon\Carbon::parse($booking->pickup_at)->format('M d, Y h:i A') : 'N/A' }}
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Return</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ $booking->return_at ? \Carbon\Carbon::parse($booking->return_at)->format('M d, Y h:i A') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Issue Status</h3>
                        <p class="text-sm text-blue-800">
                            New reports are automatically saved with the
                            <span class="font-semibold">Pending</span> issue status.
                            You can update the issue status later from the return issue management page.
                        </p>
                    </div>
                </div>

                <div class="xl:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">Issue Details</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Fill in the report details and upload photos if available.
                            </p>
                        </div>

                        <form action="{{ route('staff.return-issues.store', $booking->id) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              class="p-6">
                            @csrf

                            <input type="hidden" name="status_name" value="{{ $prefillStatus ?? '' }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label for="issue_type" class="block text-sm font-medium text-gray-700 mb-2">
                                        Issue Type
                                    </label>
                                    <select id="issue_type" name="issue_type" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Select issue type</option>
                                        <option value="damage" {{ old('issue_type', ($prefillStatus ?? '') === 'Damage' ? 'damage' : '') === 'damage' ? 'selected' : '' }}>Damage</option>
                                        <option value="low_fuel" {{ old('issue_type') === 'low_fuel' ? 'selected' : '' }}>Low Fuel</option>
                                        <option value="missing_item" {{ old('issue_type') === 'missing_item' ? 'selected' : '' }}>Missing Item</option>
                                        <option value="dirty" {{ old('issue_type') === 'dirty' ? 'selected' : '' }}>Dirty Interior / Exterior</option>
                                        <option value="late_return" {{ old('issue_type') === 'late_return' ? 'selected' : '' }}>Late Return</option>
                                        <option value="checkup" {{ old('issue_type', ($prefillStatus ?? '') === 'Checkup' ? 'checkup' : '') === 'checkup' ? 'selected' : '' }}>Checkup</option>
                                        <option value="needs_repair" {{ old('issue_type', ($prefillStatus ?? '') === 'Needs Repair' ? 'needs_repair' : '') === 'needs_repair' ? 'selected' : '' }}>Needs Repair</option>
                                    </select>
                                    @error('issue_type')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Title
                                    </label>
                                    <input
                                        id="title"
                                        type="text"
                                        name="title"
                                        value="{{ old('title') }}"
                                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Example: Rear bumper scratch"
                                    >
                                    @error('title')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea
                                        id="description"
                                        name="description"
                                        rows="5"
                                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Describe the issue in detail"
                                    >{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-1">
                                    <label for="estimated_charge" class="block text-sm font-medium text-gray-700 mb-2">
                                        Estimated Charge
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm">₱</span>
                                        <input
                                            id="estimated_charge"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="estimated_charge"
                                            value="{{ old('estimated_charge', 0) }}"
                                            class="w-full border border-gray-300 rounded-xl pl-8 pr-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        >
                                    </div>
                                    @error('estimated_charge')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-1">
                                    <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                                        Upload Photos
                                    </label>
                                    <input
                                        id="photos"
                                        type="file"
                                        name="photos[]"
                                        multiple
                                        accept="image/*"
                                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                    <p class="text-xs text-gray-500 mt-2">
                                        You can upload multiple images. Accepted formats: JPG, JPEG, PNG, WEBP.
                                    </p>
                                    @error('photos')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                    @error('photos.*')
                                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                                <a href="{{ route('staff.bookings.index') }}"
                                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300 transition">
                                    Cancel
                                </a>

                                <button type="submit"
                                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition font-medium">
                                    Save Issue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection