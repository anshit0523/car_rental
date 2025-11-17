<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Status;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    // Main index function with search + filter
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'car.brand', 'status']);

        // 🔍 Search by user name or car model
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->orWhereHas('car', function($c) use ($search) {
                    $c->where('model', 'like', "%{$search}%");
                });
            });
        }

        // 🧭 Filter by status
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $bookings = $query->paginate(10);
        $statuses = Status::all();

        return view('admin.adminbooking', compact('bookings', 'statuses'));
    }

    public function updateStatus(Request $request, Booking $booking)
{
    $request->validate([
        'status_id' => 'required|exists:statuses,id',
    ]);

    $booking->update([
        'status_id' => $request->status_id,
    ]);

    return redirect()
        ->route('admin.bookings.index')
        ->with('success', 'Booking status updated successfully.');
}

}
