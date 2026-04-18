<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AvailabilityController extends Controller
{
    private function blockingStatusIds()
    {
        return Status::whereIn('name', [
            'Reserved',
            'Active',
            'Pending',
            'Confirmed',
            'Approved',
            'Pending Payment Verification',
        ])->pluck('id')->toArray();
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|date_format:H:i',
            'return_date' => 'required|date',
            'return_time' => 'required|date_format:H:i',
        ]);

        $pickup = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['pickup_date'] . ' ' . $validated['pickup_time']
        );

        $return = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['return_date'] . ' ' . $validated['return_time']
        );

        if ($return->lte($pickup)) {
            return response()->json([
                'success' => false,
                'available' => false,
                'message' => 'Return date/time must be after pickup date/time.',
            ], 422);
        }

        $statusIds = $this->blockingStatusIds();

        $conflict = Booking::with('status')
            ->where('car_id', $validated['car_id'])
            ->whereIn('status_id', $statusIds)
            ->where(function ($query) use ($pickup, $return) {
                $query->where('pickup_at', '<', $return)
                      ->where('return_at', '>', $pickup);
            })
            ->first();

        if ($conflict) {
            return response()->json([
                'success' => true,
                'available' => false,
                'message' => 'This car is not available for the selected date and time.',
                'conflict' => [
                    'booking_id' => $conflict->id,
                    'status' => $conflict->status->name ?? 'N/A',
                    'pickup_at' => optional($conflict->pickup_at)->format('M d, Y h:i A'),
                    'return_at' => optional($conflict->return_at)->format('M d, Y h:i A'),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'available' => true,
            'message' => 'Car is available for the selected date and time.',
        ]);
    }

    public function unavailableDates($carId)
    {
        Car::findOrFail($carId);

        $statusIds = $this->blockingStatusIds();

        $bookings = Booking::where('car_id', $carId)
            ->whereIn('status_id', $statusIds)
            ->get(['pickup_at', 'return_at']);

        $dates = [];

        foreach ($bookings as $b) {
            $bookingStart = Carbon::parse($b->pickup_at);
            $bookingEnd = Carbon::parse($b->return_at);

            $cursor = $bookingStart->copy()->startOfDay();
            $lastDay = $bookingEnd->copy()->startOfDay();

            while ($cursor <= $lastDay) {
                $dayStart = $cursor->copy()->startOfDay();
                $dayEnd = $cursor->copy()->endOfDay();

                $blocksWholeDay = $bookingStart->lte($dayStart) && $bookingEnd->gte($dayEnd);

                if ($blocksWholeDay) {
                    $dates[] = $cursor->format('Y-m-d');
                }

                $cursor->addDay();
            }
        }

        return response()->json(array_values(array_unique($dates)));
    }
}