<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarType;
use App\Models\FuelType;
use App\Models\Status;
use App\Models\Transmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserBrowseCarApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Car::query()
            ->active()
            ->filterPrice($request->min_price, $request->max_price)
            ->filterBrand($request->brand_id)
            ->filterFuelType($request->fuel_type_id)
            ->filterTransmission($request->transmission_id)
            ->filterSeats($request->min_seats)
            ->filterCarType($request->car_type_id);

        $this->applySorting($query, $request->get('sort_by', 'price_low'));

        $cars = $query->with(['brand', 'fuelType', 'transmission', 'carType'])
            ->paginate($request->get('per_page', 9));

        return response()->json([
            'success' => true,
            'cars' => $cars,
            'filters' => $this->filters(),
        ]);
    }

    public function show($id)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission', 'carType'])->findOrFail($id);

        if (!$car->active) {
            return response()->json([
                'success' => false,
                'message' => 'Car is not available for rent.',
            ], 404);
        }

        $relatedCars = Car::active()
            ->with(['brand', 'fuelType', 'transmission', 'carType'])
            ->where('brand_id', $car->brand_id)
            ->where('id', '!=', $id)
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'car' => $this->formatCar($car),
            'related_cars' => $relatedCars->map(fn ($car) => $this->formatCar($car)),
            'service_types' => DB::table('service_types')->orderBy('id')->get(),
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
            ->filterPrice($request->min_price, $request->max_price)
            ->filterBrand($request->brand_id)
            ->filterFuelType($request->fuel_type_id)
            ->filterTransmission($request->transmission_id)
            ->filterSeats($request->min_seats)
            ->filterCarType($request->car_type_id);

        $this->applyAvailabilityFilter($query, $validated);
        $this->applySorting($query, $request->get('sort_by', 'price_low'));

        $cars = $query->with(['brand', 'fuelType', 'transmission', 'carType'])
            ->paginate($request->get('per_page', 9));

        return response()->json([
            'success' => true,
            'cars' => $cars,
            'filters' => $this->filters(),
            'search_params' => $validated,
        ]);
    }

    private function blockingStatusIds(): array
    {
        return Status::whereIn('name', [
            'Reserved',
            'Active',
            'Confirmed',
            'Pending Payment',
            'Pending Payment Verification',
        ])->pluck('id')->toArray();
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

    private function applySorting(Builder $query, string $sortBy): void
    {
        match ($sortBy) {
            'price_high' => $query->orderByDesc('price_per_day'),
            'newest' => $query->orderByDesc('created_at'),
            'price_low' => $query->orderBy('price_per_day'),
            default => $query->orderBy('price_per_day'),
        };
    }

    private function filters(): array
    {
        return [
            'brands' => Brand::all(),
            'fuel_types' => FuelType::all(),
            'transmissions' => Transmission::all(),
            'car_types' => CarType::all(),
            'total_count' => Car::active()->count(),
        ];
    }

    private function formatCar($car): array
    {
        $images = is_array($car->images)
            ? $car->images
            : json_decode($car->images ?? '[]', true);

        $images = is_array($images) ? $images : [];

        $imageUrls = collect($images)->map(function ($image) {
            if (!$image) {
                return null;
            }

            if (filter_var($image, FILTER_VALIDATE_URL)) {
                return $image;
            }

            $cleanPath = ltrim(str_replace('storage/', '', $image), '/');

            return config('filesystems.default') === 's3'
                ? Storage::disk('s3')->url($cleanPath)
                : asset('storage/' . $cleanPath);
        })->filter()->values();

        return [
            'id' => $car->id,
            'brand' => $car->brand,
            'model' => $car->model,
            'price_per_day' => $car->price_per_day,
            'seats' => $car->seats,
            'plate_number' => $car->plate_number ?? null,
            'fuel_type' => $car->fuelType,
            'transmission' => $car->transmission,
            'car_type' => $car->carType,
            'images' => $imageUrls,
            'active' => $car->active,
        ];
    }
}