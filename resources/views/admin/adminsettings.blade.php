@extends('layouts.adminlayout')

@section('content')
@php
    $buildImageUrl = function ($path) {
        if (! is_string($path) || empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $cleanPath = ltrim(str_replace('storage/', '', $path), '/');

        return config('filesystems.default') === 's3'
            ? \Illuminate\Support\Facades\Storage::disk('s3')->url($cleanPath)
            : asset('storage/' . $cleanPath);
    };

    $gcashQrUrl = $setting->gcash_qr_image
        ? $buildImageUrl($setting->gcash_qr_image)
        : null;
@endphp

<div class="h-screen overflow-y-auto bg-gray-50">
    <div class="max-w-4xl mx-auto px-6 py-10 pb-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Payment Settings</h1>
            <p class="text-gray-500 mt-1">Manage GCash and bank credentials shown to users.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.payment-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-xl font-bold mb-4">GCash Settings</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">GCash Account Name</label>
                        <input
                            type="text"
                            name="gcash_account_name"
                            value="{{ old('gcash_account_name', $setting->gcash_account_name) }}"
                            class="w-full border rounded-lg px-4 py-2"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">GCash Number</label>
                        <input
                            type="text"
                            name="gcash_number"
                            value="{{ old('gcash_number', $setting->gcash_number) }}"
                            class="w-full border rounded-lg px-4 py-2"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">GCash QR Image</label>
                        <input
                            type="file"
                            name="gcash_qr_image"
                            accept="image/*"
                            class="w-full border rounded-lg px-4 py-2"
                        >

                        @if($gcashQrUrl)
                            <div class="mt-4">
                                <img
                                    src="{{ $gcashQrUrl }}"
                                    alt="GCash QR"
                                    class="w-40 border rounded-lg p-2 bg-white"
                                    onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';"
                                >
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-xl font-bold mb-4">Bank Settings</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Bank Name</label>
                        <input
                            type="text"
                            name="bank_name"
                            value="{{ old('bank_name', $setting->bank_name) }}"
                            class="w-full border rounded-lg px-4 py-2"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Bank Account Name</label>
                        <input
                            type="text"
                            name="bank_account_name"
                            value="{{ old('bank_account_name', $setting->bank_account_name) }}"
                            class="w-full border rounded-lg px-4 py-2"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2">Bank Account Number</label>
                        <input
                            type="text"
                            name="bank_account_number"
                            value="{{ old('bank_account_number', $setting->bank_account_number) }}"
                            class="w-full border rounded-lg px-4 py-2"
                        >
                    </div>
                </div>
            </div>

            <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg"
            >
                Save Settings
            </button>
        </form>
    </div>
</div>
@endsection