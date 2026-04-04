<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCarBrowseController extends Controller
{
    public function index(Request $request)
    {
        $hasFilters = $this->hasBrowseFilters($request);

        $query = $hasFilters
            ? Car::query()
                ->active()
                ->filterLocation($request->location)
                ->filterPrice($request->min_price, $request->max_price)
                ->filterBrand($request->brand_id)
                ->filterFuelType($request->fuel_type_id)
                ->filterTransmission($request->transmission_id)
                ->filterSeats($request->min_seats)
            : Car::query()->whereRaw('1 = 0');

        if ($hasFilters) {
            $this->applySorting($query, $request->get('sort_by', 'price_low'));
        }

        $cars = $query->with(['brand', 'fuelType', 'transmission'])
            ->paginate(9)
            ->appends($request->query());

        return view('user.userbrowse', [
            'cars' => $cars,
            'brands' => $this->getBrands(),
            'fuelTypes' => $this->getFuelTypes(),
            'transmissions' => $this->getTransmissions(),
            'totalCount' => Car::active()->count(),
        ]);
    }

    public function show($id)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission'])->findOrFail($id);

        if (!$car->active) {
            return redirect()->route('user.browse')
                ->with('error', 'Car is not available for rent');
        }

        $relatedCars = Car::active()
            ->where('brand_id', $car->brand_id)
            ->where('id', '!=', $id)
            ->limit(5)
            ->get();

        $serviceTypes = DB::table('service_types')->orderBy('id')->get();

        return view('user.usercardetails', [
            'car' => $car,
            'relatedCars' => $relatedCars,
            'serviceTypes' => $serviceTypes,
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
           'pickup_date' => 'required|date|after_or_equal:today',
           'return_date' => 'required|date|after:pickup_date',
            'time' => 'nullable|date_format:H:i',
        ]);

        $query = Car::query()
            ->active()
            ->filterLocation($request->location)
            ->filterPrice($request->min_price, $request->max_price)
            ->filterBrand($request->brand_id)
            ->filterFuelType($request->fuel_type_id)
            ->filterTransmission($request->transmission_id)
            ->filterSeats($request->min_seats);

        $this->applyAvailabilityFilter($query, $validated);
        $this->applySorting($query, $request->get('sort_by', 'price_low'));

        $cars = $query->with(['brand', 'fuelType', 'transmission'])
            ->paginate(9)
            ->appends($request->query());

        return view('user.userbrowse', [
            'cars' => $cars,
            'brands' => $this->getBrands(),
            'fuelTypes' => $this->getFuelTypes(),
            'transmissions' => $this->getTransmissions(),
            'totalCount' => $cars->total(),
            'searchParams' => $validated,
        ]);
    }

    private function hasBrowseFilters(Request $request): bool
    {
        return $request->filled('location') ||
            $request->filled('min_price') ||
            $request->filled('max_price') ||
            $request->filled('brand_id') ||
            $request->filled('fuel_type_id') ||
            $request->filled('transmission_id') ||
            $request->filled('min_seats');
    }

    private function applySorting(Builder $query, string $sortBy): void
    {
        match ($sortBy) {
            'price_high' => $query->orderByDesc('price_per_day'),
            'newest' => $query->orderByDesc('created_at'),
            'price_low' => $query->orderBy('price_per_day'),
            default => $query->orderBy('price_per_day'),
        };
    }

    private function applyAvailabilityFilter(Builder $query, array $validated): void
    {
        $pickupDate = $validated['pickup_date'];
        $returnDate = $validated['return_date'];

        $query->whereDoesntHave('bookings', function ($bookingQuery) use ($pickupDate, $returnDate) {
            $bookingQuery
                ->where(function ($overlapQuery) use ($pickupDate, $returnDate) {
                    $overlapQuery
                        ->where('pickup_at', '<', $returnDate)
                        ->where('return_at', '>', $pickupDate);
                })
                ->whereIn('status_id', [1, 2]);
        });
    }

    private function getBrands()
    {
        return Brand::all();
    }

    private function getFuelTypes()
    {
        return FuelType::all();
    }

    private function getTransmissions()
    {
        return Transmission::all();
    }
}