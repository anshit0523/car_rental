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

           <!-- Cars Table -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox" class="w-4 h-4 rounded">
                        </th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Photo</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Total Booking</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Price per day</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cars as $car)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <input type="checkbox" class="w-4 h-4 rounded">
                            </td>
                            <td class="px-6 py-4">
                                @if($car->images && count(json_decode($car->images)) > 0)
                                    <img src="{{ asset('storage/' . json_decode($car->images)[0]) }}" alt="{{ $car->model }}" class="w-16 h-10 object-cover rounded">
                                @else
                                    <div class="w-16 h-10 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                {{ $car->brand->name ?? 'N/A' }} {{ $car->model }}
                            </td>
                            <td class="px-6 py-4 text-sm text-blue-600 font-semibold">{{ $car->bookings_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">₱ {{ number_format($car->price_per_day, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($car->active)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full inline-flex items-center gap-1">
                                        <i class="fas fa-check text-green-600"></i> Active
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full inline-flex items-center gap-1">
                                        <i class="fas fa-times text-red-600"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
    <div class="flex justify-center gap-2">
        <!-- Edit Button -->
        <button class="editCarBtn w-8 h-8 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded flex items-center justify-center transition"
                title="Edit"
                data-car="{{ json_encode($car) }}">
            <i class="fas fa-pen text-sm"></i>
        </button>

        <!-- Delete Form -->
        <form id="deleteForm-{{ $car->id }}" action="{{ route('admin.cars.destroy', $car->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button"
                    class="deleteCarBtn w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded flex items-center justify-center transition"
                    title="Delete"
                    data-form-id="deleteForm-{{ $car->id }}">
                <i class="fas fa-trash text-sm"></i>
            </button>
        </form>
    </div>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                                <p class="text-lg">No cars found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $cars->links() }}
            </div>
        </div>

<!-- Add/Edit Car Modal -->
<div id="addCarModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-center">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-900">Add New Car</h2>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addCarForm" action="{{ route('admin.cars.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" data-store-route="{{ route('admin.cars.store') }}" data-update-route="{{ route('admin.cars.update', ':id') }}">
            @csrf
            <input type="hidden" id="carId" name="car_id">
            <input type="hidden" id="methodField" name="_method" value="POST">

            <!-- Brand -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand *</label>
                <select name="brand_id" id="brandId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Model -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Model *</label>
                <input type="text" name="model" id="modelInput" required placeholder="e.g., Camry, Accord" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Transmission -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Transmission *</label>
                <select name="transmission_id" id="transmissionId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select Transmission</option>
                    @foreach($transmissions as $transmission)
                        <option value="{{ $transmission->id }}">{{ $transmission->type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fuel Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fuel Type *</label>
                <select name="fuel_type_id" id="fuelTypeId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Select Fuel Type</option>
                    @foreach($fuelTypes as $fuelType)
                        <option value="{{ $fuelType->id }}">{{ $fuelType->type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Seats -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seats</label>
                <input type="number" name="seats" id="seatsInput" value="4" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Price Per Day -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price Per Day (₱) *</label>
                <input type="number" name="price_per_day" id="priceInput" step="0.01" required placeholder="e.g., 99.99" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="descriptionInput" rows="4" placeholder="Car features and details..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <!-- Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Images</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">Upload multiple images (optional)</p>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="active" id="activeCheckbox" value="1" checked class="w-4 h-4 text-green-600 rounded">
                <label for="activeCheckbox" class="ml-2 text-sm font-medium text-gray-700">Active</label>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 pt-6 flex gap-3 justify-end">
                <button type="button" id="cancelBtn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span id="submitBtnText">Add Car</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Delete Car</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this car?</p>
            <div class="flex justify-center gap-3">
                <button id="deleteCancelBtn" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                    Cancel
                </button>
                <button id="deleteConfirmBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admincars.js') }}"></script>
@endsection
