<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserRentalApiController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Booking::where('user_id', $request->user()->id)
            ->with([
                'car.brand',
                'car.fuelType',
                'car.transmission',
                'status',
                'photoReceipt',
            ]);

        if ($status && strtolower($status) !== 'all') {
            $query->whereHas('status', function ($q) use ($status) {
                $q->where('name', $status);
            });
        }

        $bookings = $query->orderBy('pickup_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'bookings' => [
                'data' => $bookings->getCollection()
                    ->map(fn ($booking) => $this->formatBooking($booking))
                    ->values(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    private function formatBooking($booking): array
    {
        return [
            'id' => $booking->id,
            'pickup_at' => optional($booking->pickup_at)->toDateTimeString(),
            'return_at' => optional($booking->return_at)->toDateTimeString(),
            'total_price' => $booking->total_price,
            'final_total' => $booking->final_total,
            'points_used' => $booking->points_used,
            'discount_amount' => $booking->discount_amount,
            'service_location' => $booking->service_location,
            'status' => [
                'id' => $booking->status?->id,
                'name' => $booking->status?->name,
            ],
            'car' => [
                'id' => $booking->car?->id,
                'brand' => $booking->car?->brand?->name,
                'model' => $booking->car?->model,
                'price_per_day' => $booking->car?->price_per_day,
                'fuel_type' => $booking->car?->fuelType?->type,
                'transmission' => $booking->car?->transmission?->type,
                'images' => $this->carImages($booking->car),
            ],
            'photo_receipt' => $booking->photoReceipt,
        ];
    }

    private function carImages($car): array
    {
        if (!$car) {
            return [];
        }

        $images = is_array($car->images)
            ? $car->images
            : json_decode($car->images ?? '[]', true);

        $images = is_array($images) ? $images : [];

        return collect($images)->map(function ($image) {
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
        })->filter()->values()->toArray();
    }
}