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
    private function carDisk(): string
    {
        return config('filesystems.default') === 's3' ? 's3' : 'public';
    }

    private function shouldDeleteStoredImage($image): bool
    {
        return is_string($image)
            && $image !== ''
            && ! filter_var($image, FILTER_VALIDATE_URL);
    }

    public function cars()
    {
        $brands = Brand::all();
        $carTypes = CarType::all();
        $transmissions = Transmission::all();
        $fuelTypes = FuelType::all();
        $trackers = Tracker::orderBy('imei')->get();

        $cars = Car::with(['brand', 'carType', 'transmission', 'fuelType', 'tracker'])
            ->withCount('bookings')
            ->paginate(9);

        return view('admin.admincars', compact(
            'brands',
            'carTypes',
            'transmissions',
            'fuelTypes',
            'trackers',
            'cars'
        ));
    }

    public function create()
    {
        $brands = Brand::all();
        $carTypes = CarType::all();
        $transmissions = Transmission::all();
        $fuelTypes = FuelType::all();
        $trackers = Tracker::orderBy('imei')->get();

        $cars = Car::with(['brand', 'carType', 'transmission', 'fuelType', 'tracker'])
            ->withCount('bookings')
            ->paginate(15);

        return view('admin.cars', compact(
            'brands',
            'carTypes',
            'transmissions',
            'fuelTypes',
            'trackers',
            'cars'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'car_type_id' => 'required|exists:car_types,id',
            'transmission_id' => 'required|exists:transmissions,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'model' => 'required|string|max:255',
            'seats' => 'nullable|integer|min:1|max:10',
            'price_per_day' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'active' => 'nullable|boolean',
            'tracker_id' => 'nullable|exists:trackers,id',
        ]);

        if (!empty($validated['tracker_id'])) {
            $trackerAlreadyUsed = Car::where('tracker_id', $validated['tracker_id'])->exists();

            if ($trackerAlreadyUsed) {
                return back()
                    ->withErrors(['tracker_id' => 'This tracker is already assigned to another car.'])
                    ->withInput();
            }
        }

        $images = [];
        $disk = $this->carDisk();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('cars', $disk);
                $images[] = $path;
            }
        }

        $validated['images'] = json_encode($images);
        $validated['active'] = $request->has('active') ? 1 : 0;

        Car::create($validated);

        return redirect()->route('admin.cars')->with('success', 'Car added successfully!');
    }

    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'car_type_id' => 'required|exists:car_types,id',
            'transmission_id' => 'required|exists:transmissions,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'model' => 'required|string|max:255',
            'seats' => 'nullable|integer|min:1|max:10',
            'price_per_day' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'active' => 'nullable|boolean',
            'tracker_id' => 'nullable|exists:trackers,id',
        ]);

        if (!empty($validated['tracker_id'])) {
            $trackerAlreadyUsed = Car::where('tracker_id', $validated['tracker_id'])
                ->where('id', '!=', $car->id)
                ->exists();

            if ($trackerAlreadyUsed) {
                return back()
                    ->withErrors(['tracker_id' => 'This tracker is already assigned to another car.'])
                    ->withInput();
            }
        }

        $disk = $this->carDisk();

        if ($request->hasFile('images')) {
            if ($car->images) {
                $oldImages = json_decode($car->images, true);

                if (is_array($oldImages)) {
                    foreach ($oldImages as $oldImage) {
                        if ($this->shouldDeleteStoredImage($oldImage)) {
                            Storage::disk($disk)->delete($oldImage);
                        }
                    }
                }
            }

            $images = [];

            foreach ($request->file('images') as $image) {
                $path = $image->store('cars', $disk);
                $images[] = $path;
            }

            $validated['images'] = json_encode($images);
        } else {
            unset($validated['images']);
        }

        $validated['active'] = $request->has('active') ? 1 : 0;

        $car->update($validated);

        return redirect()->route('admin.cars')->with('success', 'Car updated successfully!');
    }

    public function destroy($id)
    {
        $car = Car::findOrFail($id);
        $disk = $this->carDisk();

        if ($car->images) {
            $oldImages = json_decode($car->images, true);

            if (is_array($oldImages)) {
                foreach ($oldImages as $oldImage) {
                    if ($this->shouldDeleteStoredImage($oldImage)) {
                        Storage::disk($disk)->delete($oldImage);
                    }
                }
            }
        }

        $car->delete();

        return redirect()->route('admin.cars')->with('success', 'Car deleted successfully!');
    }
}