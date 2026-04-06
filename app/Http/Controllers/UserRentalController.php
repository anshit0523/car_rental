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

  public function pending()
{
    $bookings = Booking::where('user_id', auth()->id())
        ->whereIn('status_id', [5,17]) 
        ->with(['car.brand', 'car.fuelType', 'car.transmission', 'status'])
        ->orderBy('pickup_at', 'desc')
        ->paginate(10);

    return view('user.userrentals', [
        'bookings' => $bookings,
        'status' => 'Pending',
    ]);
}

public function failed()
{
    $failedStatus = \App\Models\Status::where('name', 'Failed')->firstOrFail();

    $bookings = \App\Models\Booking::with([
            'car.brand',
            'car.fuelType',
            'car.transmission',
            'photoReceipt',
        ])
        ->where('user_id', auth()->id())
        ->where('status_id', $failedStatus->id)
        ->latest()
        ->paginate(10);

    return view('user.userrentals', compact('bookings'));
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

   
}