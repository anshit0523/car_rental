@extends('layouts.adminlayout')
    
@section('content')

    <!-- Main Content -->
        <div class="flex-1 p-6 lg:p-8 w-full lg:ml-0">
            <!-- Page Header -->
            <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Fleet Management</h1>
                    <p class="text-gray-600">Manage your car inventory</p>
                </div>
                <button id="addCarBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition flex items-center gap-2 w-full lg:w-auto justify-center">
                    <i class="fas fa-plus"></i>
                    Add Car
                </button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-600">
                    <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Total Cars</h6>
                    <div class="text-3xl font-bold text-gray-900">65</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-600">
                    <h6 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Total Bookings</h6>
                    <div class="text-3xl font-bold text-gray-900">1,245</div>
                </div>
            </div>

           <!-- Cars Grid -->
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($cars as $car)
        <!-- Car Card -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:-translate-y-1 hover:shadow-lg transition duration-300">
            <!-- Car Image Header -->
            <div class="h-32 bg-gradient-to-r from-indigo-600 to-purple-700">
                 @if($car->images && count(json_decode($car->images)) > 0)
                    @php
                        $images = json_decode($car->images);
                        $firstImage = $images[0];
                    @endphp
                    <img src="{{ asset('storage/' . $firstImage) }}" alt="{{ $car->model }}" class="w-full h-full object-cover">
                @else
                    <div class="flex flex-col items-center justify-center text-white">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p class="text-sm">No Image</p>
                    </div>
                @endif
            </div>
            
            <div class="p-6">
                <!-- Car Brand & Model -->
                <h5 class="text-lg font-bold text-gray-900 mb-3">
                    {{ $car->brand->name ?? 'N/A' }} {{ $car->model }}
                </h5>
                
                <!-- Car Details -->
                <div class="text-sm text-gray-600 space-y-2 mb-4">
                    <p>
                        <i class="fas fa-cog w-4"></i>
                        {{ $car->transmission->type ?? 'N/A' }}
                    </p>
                    <p>
                        <i class="fas fa-gas-pump w-4"></i>
                        {{ $car->fuelType->type ?? 'N/A' }}
                    </p>
                    <p>
                        <i class="fas fa-calendar-check w-4"></i>
                        {{ $car->bookings_count ?? 0 }} Bookings
                    </p>
                    <p>
                        <i class="fas fa-users w-4"></i>
                        {{ $car->seats }} Seats
                    </p>
                    <p>
                        <i class="fas fa-dollar-sign w-4"></i>
                        &#8369;{{ number_format($car->price_per_day, 2) }}/Day
                    </p>
                </div>

                <!-- Active Status -->
                <div class="mb-4">
                    @if($car->active)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">Active</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Inactive</span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
           
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <p class="text-gray-500 text-lg">No cars found</p>
        </div>
    @endforelse
</div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                <nav class="flex items-center gap-1 flex-wrap justify-center">
                    <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="px-3 py-2 bg-indigo-600 text-white rounded-lg">1</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">2</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">3</button>
                    <button class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </nav>
            </div>
        </div>

<!-- Add Car Modal -->
<div id="addCarModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Add New Car</h2>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addCarForm" action="{{ route('admin.cars.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Brand -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand *</label>
                <select name="brand_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Model -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Model *</label>
                <input type="text" name="model" required placeholder="e.g., Camry, Accord" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Transmission -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Transmission *</label>
                <select name="transmission_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select Transmission</option>
                    @foreach($transmissions as $transmission)
                        <option value="{{ $transmission->id }}">{{ $transmission->type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fuel Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fuel Type *</label>
                <select name="fuel_type_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select Fuel Type</option>
                    @foreach($fuelTypes as $fuelType)
                        <option value="{{ $fuelType->id }}">{{ $fuelType->type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Seats -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seats</label>
                <input type="number" name="seats" value="4" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Price Per Day -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Per Day ($) *</label>
                <input type="number" name="price_per_day" step="0.01" required placeholder="e.g., 99.99" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" placeholder="Car features and details..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <!-- Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Images</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">Upload multiple images (optional)</p>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="active" value="1" checked id="active" class="w-4 h-4 text-green-600 rounded">
                <label for="active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 pt-6 flex gap-3 justify-end">
                <button type="button" id="cancelBtn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Add Car
                </button>
            </div>
        </form>
    </div>
</div>


@endsection

@section('scripts')
<script src="{{ asset('js/admincars.js') }}"></script>

@endsection
    