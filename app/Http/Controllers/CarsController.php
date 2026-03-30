<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Brand;
use App\Models\Tracker;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarsController extends Controller
{
    public function create()
    {
        $brands = Brand::all();
        $transmissions = Transmission::all();
        $fuelTypes = FuelType::all();

        $trackers = Tracker::orderBy('imei')->get();

        $cars = Car::with(['brand', 'transmission', 'fuelType', 'tracker'])
            ->withCount('bookings')
            ->paginate(15);

        return view('admin.cars', compact(
            'brands',
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
            'transmission_id' => 'required|exists:transmissions,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'model' => 'required|string|max:255',
            'seats' => 'nullable|integer|min:1|max:10',
            'price_per_day' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('cars', 'public');
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
            'transmission_id' => 'required|exists:transmissions,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'model' => 'required|string|max:255',
            'seats' => 'nullable|integer|min:1|max:10',
            'price_per_day' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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

        $images = [];

        if ($request->hasFile('images')) {
            if ($car->images) {
                $oldImages = json_decode($car->images, true);
                if (is_array($oldImages)) {
                    foreach ($oldImages as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }
            }

            foreach ($request->file('images') as $image) {
                $path = $image->store('cars', 'public');
                $images[] = $path;
            }
        } else {
            $images = $car->images ? json_decode($car->images, true) : [];
        }

        $validated['images'] = json_encode($images);
        $validated['active'] = $request->has('active') ? 1 : 0;

        $car->update($validated);

        return redirect()->route('admin.cars')->with('success', 'Car updated successfully!');
    }

    public function destroy($id)
    {
        $car = Car::findOrFail($id);

        if ($car->images) {
            $oldImages = json_decode($car->images, true);
            if (is_array($oldImages)) {
                foreach ($oldImages as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        }

        $car->delete();

        return redirect()->route('admin.cars')->with('success', 'Car deleted successfully!');
    }
}