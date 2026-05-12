<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarType;
use App\Models\FuelType;
use App\Models\Tracker;
use App\Models\Transmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarsController extends Controller
{
    private function carsRoute(): string
    {
        return request()->routeIs('manager.*')
            ? 'manager.cars'
            : 'admin.cars';
    }

    private function checkAccess(): void
    {
        if (!auth()->check() || !in_array((int) auth()->user()->role_id, [1, 4])) {
            abort(403, 'Access denied.');
        }
    }

    public function cars()
    {
        $this->checkAccess();

        $cars = Car::with(['brand', 'carType', 'transmission', 'fuelType', 'tracker'])
            ->withCount('bookings')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $brands = Brand::orderBy('name')->get();
        $carTypes = CarType::orderBy('name')->get();
        $transmissions = Transmission::orderBy('type')->get();
        $fuelTypes = FuelType::orderBy('type')->get();

        $trackers = Tracker::orderBy('imei')->get();

        return view('admin.admincars', compact(
            'cars',
            'brands',
            'carTypes',
            'transmissions',
            'fuelTypes',
            'trackers'
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'car_type_id' => ['required', 'exists:car_types,id'],
            'model' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:20'],
            'transmission_id' => ['required', 'exists:transmissions,id'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'seats' => ['nullable', 'integer', 'min:2', 'max:18'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'tracker_id' => ['nullable', 'exists:trackers,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('cars', config('filesystems.default'));
            }
        }

        Car::create([
            'brand_id' => $validated['brand_id'],
            'car_type_id' => $validated['car_type_id'],
            'model' => $validated['model'],
            'plate_number' => $request->filled('plate_number')
                ? strtoupper($request->plate_number)
                : null,
            'transmission_id' => $validated['transmission_id'],
            'fuel_type_id' => $validated['fuel_type_id'],
            'seats' => $validated['seats'] ?? 4,
            'price_per_day' => $validated['price_per_day'],
            'description' => $validated['description'] ?? null,
            'tracker_id' => $validated['tracker_id'] ?? null,
            'images' => $imagePaths,
            'active' => $request->has('active'),
        ]);

        return redirect()
            ->route($this->carsRoute())
            ->with('success', 'Car added successfully.');
    }

    public function edit($id)
    {
        $this->checkAccess();

        $car = Car::with(['brand', 'carType', 'transmission', 'fuelType', 'tracker'])->findOrFail($id);

        return response()->json($car);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess();

        $car = Car::findOrFail($id);

        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'car_type_id' => ['required', 'exists:car_types,id'],
            'model' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:20'],
            'transmission_id' => ['required', 'exists:transmissions,id'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'seats' => ['nullable', 'integer', 'min:2', 'max:18'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'tracker_id' => ['nullable', 'exists:trackers,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $imagePaths = [];

        if (is_array($car->images)) {
            $imagePaths = $car->images;
        } elseif (is_string($car->images)) {
            $decodedImages = json_decode($car->images, true);
            $imagePaths = is_array($decodedImages) ? $decodedImages : [];
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('cars', config('filesystems.default'));
            }
        }

        $car->update([
            'brand_id' => $validated['brand_id'],
            'car_type_id' => $validated['car_type_id'],
            'model' => $validated['model'],
            'plate_number' => $request->filled('plate_number')
                ? strtoupper($request->plate_number)
                : null,
            'transmission_id' => $validated['transmission_id'],
            'fuel_type_id' => $validated['fuel_type_id'],
            'seats' => $validated['seats'] ?? 4,
            'price_per_day' => $validated['price_per_day'],
            'description' => $validated['description'] ?? null,
            'tracker_id' => $validated['tracker_id'] ?? null,
            'images' => $imagePaths,
            'active' => $request->has('active'),
        ]);

        return redirect()
            ->route($this->carsRoute())
            ->with('success', 'Car updated successfully.');
    }

    public function destroy($id)
    {
        $this->checkAccess();

        $car = Car::findOrFail($id);

        $car->delete();

        return redirect()
            ->route($this->carsRoute())
            ->with('success', 'Car deleted successfully.');
    }
}