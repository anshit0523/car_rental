<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class UserRentalController extends Controller
{
    public function active()
    {
        return $this->listByStatus('Active');
    }

    public function upcoming()
    {
        // After admin approves payment
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

    public function pending()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->whereHas('status', function ($q) {
                $q->where('name', 'Pending Payment Verification');
            })
            ->with([
                'car.brand',
                'car.fuelType',
                'car.transmission',
                'status',
                'photoReceipt',
            ])
            ->orderBy('pickup_at', 'desc')
            ->paginate(10);

        return view('user.userrentals', [
            'bookings' => $bookings,
            'status' => 'Pending Payment Verification',
        ]);
    }

    public function failed()
    {
        return $this->listByStatus('Failed');
    }

    private function listByStatus(string $statusName)
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->whereHas('status', function ($q) use ($statusName) {
                $q->where('name', $statusName);
            })
            ->with([
                'car.brand',
                'car.fuelType',
                'car.transmission',
                'status',
                'photoReceipt',
            ])
            ->orderBy('pickup_at', 'desc')
            ->paginate(10);

        return view('user.userrentals', [
            'bookings' => $bookings,
            'status' => $statusName,
        ]);
    }
}