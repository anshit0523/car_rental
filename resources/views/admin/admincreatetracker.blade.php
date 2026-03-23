@extends('layouts.adminlayout')

@section('content')
<div class="h-screen overflow-y-auto bg-gray-50">
    <div class="max-w-4xl mx-auto px-6 py-10 pb-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Add New Tracker</h1>
            <p class="text-gray-500 mt-1">
                Register a GPS tracker and optionally assign it to a vehicle.
            </p>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold mb-2">Please fix the following:</p>
                <ul class="list-disc ml-5 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="p-8">

                <form action="{{ route('admin.trackers.store') }}" method="POST" class="space-y-8">
                    @csrf

                    {{-- Section: Device Info --}}
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Device Information</h2>

                        <div class="space-y-5">

                            {{-- IMEI --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Device Identifier / IMEI <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="imei"
                                    value="{{ old('imei') }}"
                                    required
                                    placeholder="e.g. 31702326"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    Traccar device ID will be detected automatically from this IMEI.
                                </p>
                            </div>

                        </div>
                    </div>

                    {{-- Section: Assignment --}}
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Assignment</h2>

                        <select
                            name="car_id"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Select car (optional)</option>
                            @foreach($cars as $car)
                                <option value="{{ $car->id }}" {{ old('car_id') == $car->id ? 'selected' : '' }}>
                                    {{ $car->model }} (ID: {{ $car->id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Section: Tracker Details --}}
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Tracker Details</h2>

                        <div class="grid md:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Provider
                                </label>
                                <input
                                    type="text"
                                    name="provider"
                                    value="{{ old('provider','sinotrack') }}"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Model / Name
                                </label>
                                <input
                                    type="text"
                                    name="model"
                                    value="{{ old('model') }}"
                                    placeholder="ST-901 / Phone"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                        </div>
                    </div>

                    {{-- Section: SIM Info --}}
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">SIM Information</h2>

                        <div class="grid md:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    SIM Number
                                </label>
                                <input
                                    type="text"
                                    name="sim_number"
                                    value="{{ old('sim_number') }}"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Network
                                </label>
                                <input
                                    type="text"
                                    name="sim_network"
                                    value="{{ old('sim_network') }}"
                                    placeholder="Globe / Smart / DITO"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                        </div>

                        <div class="mt-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                APN
                            </label>
                            <input
                                type="text"
                                name="apn"
                                value="{{ old('apn') }}"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    {{-- Active Toggle --}}
                    <div class="flex items-center justify-between border rounded-lg px-4 py-3 bg-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Active</p>
                            <p class="text-xs text-gray-500">Enable this tracker</p>
                        </div>
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}
                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                    </div>

                    {{-- Submit --}}
                    <div class="pt-4 flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 bg-[#ff4d00] hover:bg-orange-500
                                   text-white text-sm font-semibold px-6 py-2.5 rounded-lg shadow-sm transition"
                        >
                            Save Tracker
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Tip --}}
        <div class="mt-5 text-xs text-gray-500">
            Tip: Traccar device ID is now fetched automatically from the IMEI during save.
        </div>

    </div>
</div>
@endsection