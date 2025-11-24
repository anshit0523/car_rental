<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Brand;
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
    $cars = Car::with(['brand', 'transmission', 'fuelType'])->paginate(15);
    
    return view('admin.cars', compact('brands', 'transmissions', 'fuelTypes','cars'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'brand_id' => 'required|exists:brands,id',
        'transmission_id' => 'required|exists:transmissions,id',
        'fuel_type_id' => 'required|exists:fuel_types,id',
        'model' => 'required|string|max:255',
        'seats' => 'integer|min:1|max:10',
        'price_per_day' => 'required|numeric|min:0.01',
        'description' => 'nullable|string',
        'images' => 'nullable|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        'active' => 'boolean',
    ]);

    // Handle images upload
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
        'seats' => 'integer|min:1|max:10',
        'price_per_day' => 'required|numeric|min:0.01',
        'description' => 'nullable|string',
        'images' => 'nullable|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        'active' => 'boolean',
    ]);

    // Handle images upload - REPLACE old images
    $images = [];
    
    if ($request->hasFile('images')) {
        // Delete old images from storage
        if ($car->images) {
            $oldImages = json_decode($car->images, true);
            foreach ($oldImages as $oldImage) {
                \Storage::disk('public')->delete($oldImage);
            }
        }
        
        // Upload new images
        foreach ($request->file('images') as $image) {
            $path = $image->store('cars', 'public');
            $images[] = $path;
        }
    } else {
        // If no new images uploaded, keep the old ones
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
    $car->delete();

    return redirect()->route('admin.cars')->with('success', 'Car deleted successfully!');
}

}
