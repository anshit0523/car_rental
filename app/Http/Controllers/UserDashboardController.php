<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        try {
            $user = auth()->user();

            // Get active rentals count
            $activeRentals = $user->bookings()->where('status_id', 2)->count();

            // Get total spent
            $totalSpent = $user->bookings()->whereIn('status_id', [3, 6])->sum('total_price');

            // Get completed trips
            $completedTrips = $user->bookings()->where('status_id', 3)->count();

            // Get loyalty points
            $points_balance = $completedTrips * 50;

            // Get current active rentals with car and status details
            $currentRentals = Booking::where('user_id', $user->id)
                ->whereIn('status_id', [2, 3])
                ->with(['car.brand', 'status'])
                ->orderBy('pickup_at', 'desc')
                ->paginate(3);

            // Get recent activity
            $recentActivity = $user->bookings()
                ->with(['car.brand', 'status'])
                ->latest()
                ->limit(3)
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
                ->limit(3)
                ->get()
                ->map(function($booking) {
                    return (object)[
                        'event' => $booking->car->brand->name . ' Pickup',
                        'date' => $booking->pickup_at->format('M d, Y \a\t h:i A')
                    ];
                });

            return view('user.userdashboard', [
                'activeRentals' => $activeRentals,
                'totalSpent' => $totalSpent,
                'completedTrips' => $completedTrips,
                'points_balance' => $points_balance,
                'currentRentals' => $currentRentals,
                'recentActivity' => $recentActivity,
                'upcomingEvents' => $upcomingEvents
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    public function rentals()
    {
        try {
            $bookings = auth()->user()->bookings()
                ->with(['car.brand', 'status'])
                ->where('status_id', '!=', 1)
                ->orderBy('pickup_at', 'desc')
                ->paginate(10);

            return view('user.userrentals', compact('bookings'));
        } catch (\Exception $e) {
            \Log::error('Rentals Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading rentals');
        }
    }



    public function history()
    {
        try {
            $bookings = auth()->user()->bookings()
                ->with(['car.brand', 'status'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('user.userhistory', compact('bookings'));
        } catch (\Exception $e) {
            \Log::error('History Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading history');
        }
    }

    public function payments()
    {
        try {
            $payments = auth()->user()->bookings()
                ->with(['car.brand', 'status'])
                ->where('status_id', 1)
                ->orderBy('updated_at', 'desc')
                ->paginate(10);

            return view('user.userpayments', compact('payments'));
        } catch (\Exception $e) {
            \Log::error('Payments Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading payments');
        }
    }

    public function profile()
    {
        try {
            $user = auth()->user();
            return view('user.userprofile', compact('user'));
        } catch (\Exception $e) {
            \Log::error('Profile Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading profile');
        }
    }

    public function settings()
    {
        try {
            return view('user.usersettings');
        } catch (\Exception $e) {
            \Log::error('Settings Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading settings');
        }
    }
}