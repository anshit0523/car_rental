<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
  
    public function index()
    {
        $user = Auth::user();

        $activeRentals = $user->bookings()->where('status_id', 2)->count();

            // Get total spent
            $totalSpent = $user->bookings()->where('status_id', 1)->sum('total_price');

            // Get completed trips
            $completedTrips = $user->bookings()->where('status_id', 1)->count();

            // Get loyalty points
            $loyaltyPoints = $completedTrips * 50;

            // Get current active rentals with car and status details
            $currentRentals = Booking::where('user_id', $user->id)
                ->whereIn('status_id', [2, 3])
                ->with(['car.brand', 'status'])
                ->orderBy('pickup_at', 'desc')
                ->get();

            // Get recent activity
            $recentActivity = $user->bookings()
                ->with(['car.brand', 'status'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($booking) {
                    return (object)[
                        'activity' => $booking->status->name . ' - ' . $booking->car->brand->name . ' ' . $booking->car->model,
                        'time' => $booking->updated_at->diffForHumans()
                    ];
                });

            // Get upcoming events (pickups and returns)
            $upcomingEvents = $user->bookings()
                ->with(['car.brand', 'status'])
                ->where('pickup_at', '>=', now())
                ->orderBy('pickup_at')
                ->limit(5)
                ->get()
                ->map(function($booking) {
                    return (object)[
                        'event' => $booking->car->brand->name . ' Pickup',
                        'date' => $booking->pickup_at->format('M d, Y \a\t h:i A')
                    ];
                });

            return view('user.dashboard', [
                'activeRentals' => $activeRentals,
                'totalSpent' => $totalSpent,
                'completedTrips' => $completedTrips,
                'loyaltyPoints' => $loyaltyPoints,
                'currentRentals' => $currentRentals,
                'recentActivity' => $recentActivity,
                'upcomingEvents' => $upcomingEvents
            ]);

    }
}
