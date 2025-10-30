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

        // Fetch user’s bookings
        $bookings = Booking::with(['car', 'status'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Get summary data
        $totalBookings = $bookings->count();
        $activeBookings = $bookings->where('status.name', 'Active')->count();

        // Pass data to dashboard view
    
        return view('dashboard', );
    }
}
