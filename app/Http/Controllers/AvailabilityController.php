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
        // These statuses block dates (adjust names to match your DB)
        return Status::whereIn('name', ['Reserved', 'Active', 'Pending', 'Confirmed'])
            ->pluck('id')
            ->toArray();
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        $pickup = Carbon::parse($validated['pickup_date'])->startOfDay();
        $return = Carbon::parse($validated['return_date'])->endOfDay();

        $statusIds = $this->blockingStatusIds();

        $hasConflict = Booking::where('car_id', $validated['car_id'])
            ->whereIn('status_id', $statusIds)
            // overlap rule (same as your store logic)
            ->where('pickup_at', '<', $return)
            ->where('return_at', '>', $pickup)
            ->exists();

        return response()->json([
            'available' => !$hasConflict
        ]);
    }

    public function unavailableDates($carId)
    {
        // verify car exists
        Car::findOrFail($carId);

        $statusIds = $this->blockingStatusIds();

        $bookings = Booking::where('car_id', $carId)
            ->whereIn('status_id', $statusIds)
            ->get(['pickup_at', 'return_at']);

        $dates = [];

        foreach ($bookings as $b) {
            $start = Carbon::parse($b->pickup_at)->startOfDay();
            $end = Carbon::parse($b->return_at)->startOfDay();

            while ($start <= $end) {
                $dates[] = $start->format('Y-m-d');
                $start->addDay();
            }
        }

        $dates = array_values(array_unique($dates));

        return response()->json($dates);
    }
}