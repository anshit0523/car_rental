<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Brand;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Http\Request;

class CarsController extends Controller
{


public function create()
{
    $brands = Brand::all();
    $transmissions = Transmission::all();
    $fuelTypes = FuelType::all();
    $cars = Car::with(['brand', 'transmission', 'fuelType'])->paginate(15);
    
    return view('admin.cars', compact('brands', 'transmissions', 'fuelTypes'));
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



public function edit($id)
{
    $car = Car::findOrFail($id);
    $brands = Brand::all();
    $transmissions = Transmission::all();
    $fuelTypes = FuelType::all();
    
    return view('admin.cars-edit', compact('car', 'brands', 'transmissions', 'fuelTypes'));
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

    // Handle images upload
    $images = $car->images ? json_decode($car->images, true) : [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('cars', 'public');
            $images[] = $path;
        }
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
