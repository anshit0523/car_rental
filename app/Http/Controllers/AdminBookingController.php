<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Brand;
use App\Models\Status;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;

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

        //  Filter by status
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

public function calendar(Request $request)
    {
        $view = $request->get('view', 'weekly');
        $dateString = $request->get('date');
        
        // Set start date
        $startDate = $dateString 
            ? Carbon::parse($dateString) 
            : now();
        
        $startDate->startOfDay();

        // Generate calendar dates
        if ($view === '30days') {
            $endDate = $startDate->copy()->addDays(29);
        } else {
            $endDate = $startDate->copy()->addDays(6);
        }

        $calendarDates = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $calendarDates[] = [
                'full' => $current->copy(),
                'date' => $current->format('d'),
                'day' => $current->format('D'),
            ];
            $current->addDay();
        }

        // Get cars with relationships
    $query = Car::with([
    'brand',
    'transmission',
    'fuelType',
    'bookings' => function ($q) use ($startDate, $endDate) {
        $q->whereHas('status', function ($s) {
            $s->whereIn('name', ['confirmed', 'reserved']);
        })
        ->where(function ($date) use ($startDate, $endDate) {
            $date->whereBetween('pickup_at', [$startDate, $endDate])
                 ->orWhereBetween('return_at', [$startDate, $endDate])
                 ->orWhere(function ($overlap) use ($startDate, $endDate) {
                     $overlap->where('pickup_at', '<=', $startDate)
                             ->where('return_at', '>=', $endDate);
                 });
        })
        ->with('status');
    }
])->where('active', true);


        // Apply filters
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->get('brand_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('model', 'like', "%{$search}%")
                  ->orWhere('plate', 'like', "%{$search}%");
            });
        }

        $cars = $query->get();

        // Calculate navigation dates
        $previousWeek = $startDate->copy()->subDays($view === '30days' ? 30 : 7)->format('Y-m-d');
        $nextWeek = $startDate->copy()->addDays($view === '30days' ? 30 : 7)->format('Y-m-d');

        $brands = Brand::all();

        return view('admin.admincalendar', [
            'cars' => $cars,
            'calendarDates' => $calendarDates,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'previousWeek' => $previousWeek,
            'nextWeek' => $nextWeek,
            'brands' => $brands,
            'view' => $view,
        ]);
    }


 public function showJson(Booking $booking): JsonResponse
{
    try {
        $booking->load('user:id,name,email', 'car:id,model,brand_id', 'car.brand:id,name', 'status:id,name');

        return response()->json([
            'success' => true,
            'id' => $booking->id,
            'status' => $booking->status->name,
            'pickup_at' => $booking->pickup_at?->format('M d, Y h:i A') ?? 'N/A',
            'return_at' => $booking->return_at?->format('M d, Y h:i A') ?? 'N/A',
            'total_price' => number_format($booking->total_price ?? 0, 2),
            'user' => [
                'id' => $booking->user->id,
                'name' => $booking->user->name,
                'email' => $booking->user->email,
            ],
            'car' => [
                'name' => ($booking->car->brand->name ?? 'Unknown') . ' ' . $booking->car->model,
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], 500);
    }
}


}