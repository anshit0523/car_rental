<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserRentalController extends Controller
{
    public function active()
    {
        return $this->listByStatus('Active');
    }

    public function upcoming()
    {
        // if your system uses "Confirmed" for upcoming
        return $this->listByStatus('Confirmed');
    }

    public function completed()
    {
        return $this->listByStatus('Completed');
    }

    public function cancelled()
    {
        return $this->listByStatus('Cancelled');
    }

    private function listByStatus(string $statusName)
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->whereHas('status', function ($q) use ($statusName) {
                $q->where('name', $statusName);
            })
            ->with(['car.brand', 'car.fuelType', 'car.transmission', 'status'])
            ->orderBy('pickup_at', 'desc')
            ->paginate(10);

        return view('user.userrentals', [
            'bookings' => $bookings,
            'status' => $statusName,
        ]);
    }

    public function cancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Authorization check
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Cannot cancel within 24 hours
        if ($booking->pickup_at <= Carbon::now()->addHours(24)) {
            return back()->withErrors(['booking' => 'Cannot cancel within 24 hours of pickup.']);
        }

        $cancelledStatus = Status::firstOrCreate(['name' => 'Cancelled']);
        $booking->update(['status_id' => $cancelledStatus->id]);

        return back()->with('success', 'Booking cancelled successfully.');
    }
}