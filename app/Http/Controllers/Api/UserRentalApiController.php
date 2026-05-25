<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PhotoReceipt;
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
                'returnIssues.issueStatus',
                'returnIssues.photos',
                'returnIssues.histories.changedBy',
            ]);

        if ($status && strtolower($status) !== 'all') {
            if ($status === 'return_group') {
                $query->whereHas('status', function ($q) {
                    $q->whereIn('name', [
                        'Return',
                        'Checkup',
                        'Damage',
                        'Needs Repair',
                    ]);
                });
            } else {
                $query->whereHas('status', function ($q) use ($status) {
                    $q->where('name', $status);
                });
            }
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

    public function show(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $booking->load([
            'car.brand',
            'car.fuelType',
            'car.transmission',
            'status',
            'photoReceipt',
            'returnIssues.issueStatus',
            'returnIssues.photos',
            'returnIssues.histories.changedBy',
        ]);

        return response()->json([
            'success' => true,
            'booking' => $this->formatBooking($booking),
        ]);
    }

    private function formatBooking($booking): array
    {
        $latestReturnIssue = $booking->returnIssues
            ? $booking->returnIssues->sortByDesc('created_at')->first()
            : null;

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
            'photo_receipt' => $this->formatPhotoReceipt($booking->photoReceipt),
            'return_issue' => $this->formatReturnIssue($latestReturnIssue),
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

    private function formatPhotoReceipt($receipt): ?array
    {
        if (!$receipt || !$receipt->image_path) {
            return null;
        }

        $path = $receipt->image_path;

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $url = $path;
        } else {
            $cleanPath = ltrim(str_replace('storage/', '', $path), '/');

            $url = config('filesystems.default') === 's3'
                ? Storage::disk('s3')->url($cleanPath)
                : asset('storage/' . $cleanPath);
        }

        return [
            'id' => $receipt->id,
            'image_path' => $path,
            'image_url' => $url,
            'status' => $receipt->status,
            'payment_method' => $receipt->payment_method,
            'created_at' => optional($receipt->created_at)->toDateTimeString(),
            'updated_at' => optional($receipt->updated_at)->toDateTimeString(),
        ];
    }

    private function formatReturnIssue($returnIssue): ?array
    {
        if (!$returnIssue) {
            return null;
        }

        return [
            'id' => $returnIssue->id,
            'title' => $returnIssue->title,
            'issue_type' => $returnIssue->issue_type,
            'description' => $returnIssue->description,
            'estimated_charge' => $returnIssue->estimated_charge,
            'final_charge' => $returnIssue->final_charge,
            'reported_at' => optional($returnIssue->reported_at)->toDateTimeString(),
            'created_at' => optional($returnIssue->created_at)->toDateTimeString(),
            'updated_at' => optional($returnIssue->updated_at)->toDateTimeString(),

            'status' => [
                'id' => $returnIssue->issueStatus?->id,
                'name' => $returnIssue->issueStatus?->name,
                'label' => $returnIssue->issueStatus?->label,
            ],

            'photos' => collect($returnIssue->photos)->map(function ($photo) {
                $path = $photo->photo_path;
                $url = null;

                if ($path) {
                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                        $url = $path;
                    } else {
                        $cleanPath = ltrim(str_replace('storage/', '', $path), '/');

                        $url = config('filesystems.default') === 's3'
                            ? Storage::disk('s3')->url($cleanPath)
                            : asset('storage/' . $cleanPath);
                    }
                }

                return [
                    'id' => $photo->id,
                    'photo_path' => $path,
                    'photo_url' => $url,
                    'created_at' => optional($photo->created_at)->toDateTimeString(),
                ];
            })->values(),

            'histories' => collect($returnIssue->histories)->sortByDesc('created_at')->map(function ($history) {
                return [
                    'id' => $history->id,
                    'title' => $history->title,
                    'message' => $history->message,
                    'created_at' => optional($history->created_at)->toDateTimeString(),

                    'changed_by' => $history->changedBy ? [
                        'id' => $history->changedBy->id,
                        'name' => $history->changedBy->name,
                    ] : null,
                ];
            })->values(),
        ];
    }



}
