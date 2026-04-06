@extends('layouts.adminlayout')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Return Issue</h1>
        <p class="text-sm text-gray-600 mb-6">
            Booking #{{ $booking->id }} - {{ $booking->car->brand->name ?? 'Car' }} {{ $booking->car->model ?? '' }}
        </p>

        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-50 rounded-lg p-4">
                <p><strong>User:</strong> {{ $booking->user->name ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ $booking->status->name ?? 'N/A' }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <p><strong>Pick-up:</strong> {{ optional($booking->pickup_at)->format('M d, Y h:i A') ?? 'N/A' }}</p>
                <p><strong>Return:</strong> {{ optional($booking->return_at)->format('M d, Y h:i A') ?? 'N/A' }}</p>
            </div>
        </div>

        <form action="{{ route('admin.return-issues.store', $booking->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-2">Issue Type</label>
                <select name="issue_type" class="w-full border rounded-lg px-4 py-2">
                    <option value="">Select issue type</option>
                    <option value="damage">Damage</option>
                    <option value="low_fuel">Low Fuel</option>
                    <option value="missing_item">Missing Item</option>
                    <option value="dirty">Dirty Interior/Exterior</option>
                    <option value="late_return">Late Return</option>
                    <option value="checkup">Checkup</option>
                </select>
                @error('issue_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-2">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded-lg px-4 py-2" placeholder="Example: Rear bumper scratch">
                @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="5" class="w-full border rounded-lg px-4 py-2" placeholder="Describe the issue in detail">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium text-gray-700 mb-2">Estimated Charge</label>
                <input type="number" step="0.01" min="0" name="estimated_charge" value="{{ old('estimated_charge', 0) }}" class="w-full border rounded-lg px-4 py-2">
                @error('estimated_charge') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block font-medium text-gray-700 mb-2">Upload Photos</label>
                <input type="file" name="photos[]" multiple accept="image/*" class="w-full border rounded-lg px-4 py-2">
                @error('photos.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800">
                    Cancel
                </a>

                <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                    Save Issue
                </button>
            </div>
        </form>
    </div>
</div>
@endsection