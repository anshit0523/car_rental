<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCarBrowseController extends Controller
{
    public function index(Request $request)
{
    $hasFilters =
        $request->filled('location') ||
        $request->filled('min_price') ||
        $request->filled('max_price') ||
        $request->filled('brand_id') ||
        $request->filled('fuel_type_id') ||
        $request->filled('transmission_id') ||
        $request->filled('min_seats');

    // Default: show no cars
    $query = Car::query()->whereRaw('1 = 0');

    // Only run real car query when user applies filters
    if ($hasFilters) {
        $query = Car::query()->where('active', true);

        // Search by location
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
    }

    $cars = $query->with(['brand', 'fuelType', 'transmission'])
        ->paginate(9)
        ->appends($request->query());

    $brands = Brand::all();
    $fuelTypes = FuelType::all();
    $transmissions = Transmission::all();

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
 $serviceTypes = DB::table('service_types')->orderBy('id')->get();
 
        return view('user.usercardetails', [
            'car' => $car,
            'relatedCars' => $relatedCars,
            'serviceTypes' => $serviceTypes
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

    $cars = Car::where('active', true)
        ->whereDoesntHave('bookings', function ($query) use ($validated) {
            $query->whereBetween('pickup_at', [$validated['pickup_date'], $validated['return_date']])
                ->orWhereBetween('return_at', [$validated['pickup_date'], $validated['return_date']])
                ->whereIn('status_id', [1, 2]);
        })
        ->with(['brand', 'fuelType', 'transmission'])
        ->paginate(9)
        ->appends($request->query());

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

