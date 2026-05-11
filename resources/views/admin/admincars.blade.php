@extends('layouts.adminlayout')

@section('content')

    <div class="flex h-screen overflow-hidden">
        <div class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 to-slate-100">
            <div class="flex-1 p-6 lg:p-8 w-full lg:ml-0">
                <div class="mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Fleet Management</h1>
                        <p class="text-gray-600">Manage your car inventory</p>
                    </div>

                    <button id="addCarBtn"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition flex items-center gap-2 w-full lg:w-auto justify-center">
                        <i class="fas fa-plus"></i>
                        Add Car
                    </button>
                </div>

                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-100 border border-green-200 text-green-800 px-4 py-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-lg bg-red-100 border border-red-200 text-red-800 px-4 py-3">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Photo</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Plate No.</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Type</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tracker</th>
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
                                            @php
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
                                                $imageUrl = null;

                                                if ($firstImage) {
                                                    $cleanImagePath = ltrim(str_replace('storage/', '', $firstImage), '/');

                                                    $imageUrl = config('filesystems.default') === 's3'
                                                        ? \Illuminate\Support\Facades\Storage::disk('s3')->url($cleanImagePath)
                                                        : asset('storage/' . $cleanImagePath);
                                                }
                                            @endphp

                                            @if($imageUrl)
                                                <img
                                                    src="{{ $imageUrl }}"
                                                    alt="{{ $car->model }}"
                                                    class="w-24 h-16 object-cover rounded-lg border border-gray-200"
                                                    onerror="this.onerror=null;this.src='{{ asset('images/no-car-image.png') }}';"
                                                >
                                            @else
                                                <div class="w-24 h-16 bg-gray-200 rounded-lg flex items-center justify-center border border-gray-200">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($car->plate_number)
                                                <span class="font-semibold tracking-wide">
                                                    {{ $car->plate_number }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">No Plate</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                            <div class="font-semibold">
                                                {{ $car->brand->name ?? 'N/A' }}
                                            </div>

                                            <div>
                                                {{ $car->model }}
                                            </div>

                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ optional($car->carType)->name ?: 'No Type' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($car->tracker)
                                                <div class="font-medium">{{ $car->tracker->imei }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $car->tracker->provider ?? 'N/A' }}
                                                    {{ $car->tracker->model ?? '' }}
                                                </div>
                                            @else
                                                <span class="text-gray-400">No Tracker</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-sm text-blue-600 font-semibold">
                                            {{ $car->bookings_count ?? 0 }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            ₱ {{ number_format($car->price_per_day, 2) }}
                                        </td>

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
                                                <button
                                                    type="button"
                                                    class="editCarBtn w-8 h-8 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded flex items-center justify-center transition"
                                                    title="Edit"
                                                    data-id="{{ $car->id }}"
                                                    data-brand-id="{{ $car->brand_id }}"
                                                    data-car-type-id="{{ $car->car_type_id }}"
                                                    data-model="{{ $car->model }}"
                                                    data-plate-number="{{ $car->plate_number }}"
                                                    data-transmission-id="{{ $car->transmission_id }}"
                                                    data-fuel-type-id="{{ $car->fuel_type_id }}"
                                                    data-seats="{{ $car->seats }}"
                                                    data-price-per-day="{{ $car->price_per_day }}"
                                                    data-description="{{ $car->description }}"
                                                    data-active="{{ $car->active ? 1 : 0 }}"
                                                    data-tracker-id="{{ $car->tracker_id }}">
                                                    <i class="fas fa-pen text-sm"></i>
                                                </button>

                                                <form id="deleteForm-{{ $car->id }}"
                                                    action="{{ route('admin.cars.destroy', $car->id) }}" method="POST">
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
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                                            <p class="text-lg">No cars found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-2 border-t border-gray-200">
                        {{ $cars->links() }}
                    </div>
                </div>

                <div id="addCarModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-center">
                            <h2 id="modalTitle" class="text-2xl font-bold text-gray-900">Add New Car</h2>

                            <button id="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form id="addCarForm"
                            action="{{ route('admin.cars.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="p-6 space-y-6"
                            data-store-route="{{ route('admin.cars.store') }}"
                            data-update-route="{{ route('admin.cars.update', ':id') }}">
                            @csrf

                            <input type="hidden" id="carId" name="car_id">
                            <input type="hidden" id="methodField" name="_method" value="POST">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Brand *</label>
                                <select name="brand_id" id="brandId" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Car Type *</label>
                                <select name="car_type_id" id="carTypeId" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select Car Type</option>
                                    @foreach($carTypes as $carType)
                                        <option value="{{ $carType->id }}">{{ $carType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Model *</label>
                                <input type="text"
                                    name="model"
                                    id="modelInput"
                                    required
                                    placeholder="e.g., Camry, Accord"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Plate Number</label>
                                <input type="text"
                                    name="plate_number"
                                    id="plateNumberInput"
                                    placeholder="e.g., ABC 1234"
                                    maxlength="20"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <p class="text-xs text-gray-500 mt-1">
                                    Optional. Plate number will be saved in uppercase.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Transmission *</label>
                                <select name="transmission_id" id="transmissionId" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select Transmission</option>
                                    @foreach($transmissions as $transmission)
                                        <option value="{{ $transmission->id }}">{{ $transmission->type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fuel Type *</label>
                                <select name="fuel_type_id" id="fuelTypeId" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Select Fuel Type</option>
                                    @foreach($fuelTypes as $fuelType)
                                        <option value="{{ $fuelType->id }}">{{ $fuelType->type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Seats</label>
                                <input type="number"
                                    name="seats"
                                    id="seatsInput"
                                    value="4"
                                    min="1"
                                    max="10"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price Per Day (₱) *</label>
                                <input type="number"
                                    name="price_per_day"
                                    id="priceInput"
                                    step="0.01"
                                    required
                                    placeholder="e.g., 99.99"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description"
                                    id="descriptionInput"
                                    rows="4"
                                    placeholder="Car features and details..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Images</label>
                                <input type="file"
                                    name="images[]"
                                    multiple
                                    accept="image/*"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <p class="text-xs text-gray-500 mt-1">
                                    Upload multiple images (optional)
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tracker</label>
                                <select name="tracker_id" id="trackerId"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">No Tracker</option>
                                    @foreach($trackers as $tracker)
                                        <option value="{{ $tracker->id }}">
                                            {{ $tracker->imei }} -
                                            {{ $tracker->provider ?? 'N/A' }}
                                            {{ $tracker->model ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    Assign a tracker to this car
                                </p>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox"
                                    name="active"
                                    id="activeCheckbox"
                                    value="1"
                                    checked
                                    class="w-4 h-4 text-green-600 rounded">
                                <label for="activeCheckbox" class="ml-2 text-sm font-medium text-gray-700">
                                    Active
                                </label>
                            </div>

                            <div class="border-t border-gray-200 pt-6 flex gap-3 justify-end">
                                <button type="button" id="cancelBtn"
                                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                                    Cancel
                                </button>

                                <button type="submit"
                                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center gap-2">
                                    <i class="fas fa-save"></i>
                                    <span id="submitBtnText">Add Car</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm">
                        <div class="p-6 text-center">
                            <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Delete Car</h2>
                            <p class="text-gray-600 mb-6">Are you sure you want to delete this car?</p>

                            <div class="flex justify-center gap-3">
                                <button id="deleteCancelBtn"
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                                    Cancel
                                </button>

                                <button id="deleteConfirmBtn"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/admin/admincars.js') }}"></script>
@endsection