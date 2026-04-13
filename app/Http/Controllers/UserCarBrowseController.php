<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\FuelType;
use App\Models\CarType;
use App\Models\Status;
use App\Models\Transmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
                ->filterCarType($request->car_type_id)
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
            'carTypes' => $this->getCarTypes(),
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
            ->filterSeats($request->min_seats)
            ->filterCarType($request->car_type_id);

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
            'carTypes' => $this->getCarTypes(),
        ]);
    }

    private function blockingStatusIds(): array
    {
        return Status::whereIn('name', ['Reserved', 'Active', 'Pending', 'Confirmed'])
            ->pluck('id')
            ->toArray();
    }

    private function applyAvailabilityFilter(Builder $query, array $validated): void
    {
        $pickup = Carbon::parse($validated['pickup_date'])->startOfDay();
        $return = Carbon::parse($validated['return_date'])->endOfDay();

        $statusIds = $this->blockingStatusIds();

        $query->whereDoesntHave('bookings', function ($bookingQuery) use ($pickup, $return, $statusIds) {
            $bookingQuery
                ->whereIn('status_id', $statusIds)
                ->where('pickup_at', '<', $return)
                ->where('return_at', '>', $pickup);
        });
    }

    private function hasBrowseFilters(Request $request): bool
    {
        return $request->filled('location') ||
            $request->filled('min_price') ||
            $request->filled('max_price') ||
            $request->filled('brand_id') ||
            $request->filled('fuel_type_id') ||
            $request->filled('transmission_id') ||
            $request->filled('min_seats')||
             $request->filled('car_type_id');
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

    private function getCarTypes()
{
    return CarType::all();
}
}