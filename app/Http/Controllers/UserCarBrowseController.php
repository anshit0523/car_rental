<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Brand;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Http\Request;

class UserCarBrowseController extends Controller
{
     public function index(Request $request)
    {
        // Start query builder - only show active cars
        $query = Car::query()->where('active', true);

        // Search by location (if you have a location column)
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by price range
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price_per_day', [$request->min_price, $request->max_price]);
        } elseif ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        } elseif ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $brandIds = is_array($request->brand_id) ? $request->brand_id : [$request->brand_id];
            $query->whereIn('brand_id', $brandIds);
        }

        // Filter by fuel type
        if ($request->filled('fuel_type_id')) {
            $fuelIds = is_array($request->fuel_type_id) ? $request->fuel_type_id : [$request->fuel_type_id];
            $query->whereIn('fuel_type_id', $fuelIds);
        }

        // Filter by transmission
        if ($request->filled('transmission_id')) {
            $transmissionIds = is_array($request->transmission_id) ? $request->transmission_id : [$request->transmission_id];
            $query->whereIn('transmission_id', $transmissionIds);
        }

        // Filter by available seats
        if ($request->filled('min_seats')) {
            $query->where('seats', '>=', $request->min_seats);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'price_low');
        match ($sortBy) {
            'price_high' => $query->orderByDesc('price_per_day'),
            'newest' => $query->orderByDesc('created_at'),
            'price_low' => $query->orderBy('price_per_day'),
            default => $query->orderBy('price_per_day'),
        };

        // Pagination
        $cars = $query->with(['brand', 'fuelType', 'transmission'])->paginate(9)->appends($request->query());

        // Get filter options for the sidebar
        $brands = \App\Models\Brand::all();
        $fuelTypes = \App\Models\FuelType::all();
        $transmissions = \App\Models\Transmission::all();

        return view('user.userbrowse', [
            'cars' => $cars,
            'brands' => $brands,
            'fuelTypes' => $fuelTypes,
            'transmissions' => $transmissions,
            'totalCount' => Car::where('active', true)->count(),
        ]);
    }

    /**
     * Display a single car detail page.
     */
    public function show($id)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission'])->findOrFail($id);

        if (!$car->active) {
            return redirect()->route('user.browse')->with('error', 'Car is not available for rent');
        }

        $relatedCars = Car::where('brand_id', $car->brand_id)
            ->where('id', '!=', $id)
            ->where('active', true)
            ->limit(5)
            ->get();

        return view('user.usercardetails', [
            'car' => $car,
            'relatedCars' => $relatedCars,
        ]);
    }

    /**
     * Search cars by pickup/return dates and check availability.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after:pickup_date',
            'time' => 'nullable|date_format:H:i',
        ]);

        // Check for car availability
        $cars = Car::where('active', true)
            ->whereDoesntHave('bookings', function ($query) use ($validated) {
                $query->whereBetween('pickup_at', [$validated['pickup_date'], $validated['return_date']])
                    ->orWhereBetween('return_at', [$validated['pickup_date'], $validated['return_date']])
                    ->whereIn('status_id', [1, 2]);
            })
            ->with(['brand', 'fuelType', 'transmission'])
            ->paginate(9);

        $brands = Brand::all();
        $fuelTypes = FuelType::all();
        $transmissions = Transmission::all();

        return view('user.userbrowse', [
            'cars' => $cars,
            'brands' => $brands,
            'fuelTypes' => $fuelTypes,
            'transmissions' => $transmissions,
            'totalCount' => $cars->total(),
            'searchParams' => $validated,
        ]);
    }
}

