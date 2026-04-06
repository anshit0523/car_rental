<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Notification;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        $bookings = $query->latest()->paginate(10)->onEachSide(1);
        $statuses = Status::all();

        return view('admin.adminbooking', compact('bookings', 'statuses'));
    }


public function updateStatus(Request $request, Booking $booking)
{
    $request->validate([
        'status_id' => 'required|exists:statuses,id',
        'admin_message' => 'nullable|string|max:500',
    ]);

    $newStatus = Status::findOrFail($request->status_id);
    $currentStatus = $booking->status->name ?? null;

    $allowedTransitions = [
        'Pending' => ['Cancelled'],
        'Confirmed' => ['Cancelled'],
        'Return' => ['Checkup', 'Damage', 'Needs Repair', 'Completed'],
        'Active' => [],
        'Completed' => [],
        'Cancelled' => [],
        'Checkup' => [],
        'Damage' => [],
        'Needs Repair' => [],
        'Failed' => [],
    ];

    if (!array_key_exists($currentStatus, $allowedTransitions)) {
        return redirect()
            ->route('admin.bookings.index')
            ->with('error', 'Invalid current booking status.');
    }

    if (!in_array($newStatus->name, $allowedTransitions[$currentStatus])) {
        return redirect()
            ->route('admin.bookings.index')
            ->with('error', "Cannot change status from {$currentStatus} to {$newStatus->name}.");
    }

    $booking->update([
        'status_id' => $newStatus->id,
    ]);

    $adminMessage = trim($request->admin_message ?? '');

    // Send notification only for return processing, with optional custom note
    if ($currentStatus === 'Return') {
        $notificationMessage = "Your booking status has been updated to {$newStatus->name}.";

        if (!empty($adminMessage)) {
            $notificationMessage .= " Admin note: {$adminMessage}";
        }
$title = match ($newStatus->name) {
    'Damage' => 'Vehicle Damage Notice',
    'Needs Repair' => 'Vehicle Repair Notice',
    'Checkup' => 'Vehicle Checkup Notice',
    'Completed' => 'Booking Completed',
    default => 'Booking Return Update',
};
        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'title' => $title,
            'message' => $notificationMessage,
            'type' => 'booking',
            'link' => route('user.booking.confirmation', $booking->id),
        ]);
    }

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
     'brand:id,name',          
    'transmission:id,type', 
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

    // Apply brand filter
    if ($request->filled('brand_id')) {
        $query->where('brand_id', $request->get('brand_id'));
    }

    // Apply search filter (search model and plate)
    if ($request->filled('search')) {
        $search = $request->get('search');
        $query->where(function ($q) use ($search) {
            $q->where('model', 'like', "%{$search}%")
             
            ;
        });
    }

    $cars = $query->paginate(5);

    // Calculate navigation dates
    $previousWeek = $startDate->copy()->subDays($view === '30days' ? 30 : 7)->format('Y-m-d');
    $nextWeek = $startDate->copy()->addDays($view === '30days' ? 30 : 7)->format('Y-m-d');

    $brands = Brand::all();

    $data = [
        'cars' => $cars,
        'calendarDates' => $calendarDates,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'previousWeek' => $previousWeek,
        'nextWeek' => $nextWeek,
        'brands' => $brands,
        'view' => $view,
    ];

    // If AJAX → return full view (JavaScript will extract the table)
    if ($request->ajax()) {
        return view('admin.admincalendar', $data)->render();
    }

    return view('admin.admincalendar', $data);
}



 public function showJson(Booking $booking): JsonResponse
{
    try {
        $booking->load([
            'user:id,name,email,phone',
            'car:id,model,brand_id',
            'car.brand:id,name',
            'status:id,name',
            'serviceType:id,name',
        ]);

        return response()->json([
            'success' => true,
            'id' => $booking->id,
            'status' => $booking->status->name ?? 'N/A',
            'pickup_at' => $booking->pickup_at?->format('M d, Y h:i A') ?? 'N/A',
            'return_at' => $booking->return_at?->format('M d, Y h:i A') ?? 'N/A',
            'total_price' => number_format($booking->total_price ?? 0, 2),
            'service_type' => $booking->serviceType->name ?? 'N/A',
            'service_location' => $booking->service_location ?? 'N/A',
            'user' => [
                'id' => $booking->user->id ?? null,
                'name' => $booking->user->name ?? 'N/A',
                'email' => $booking->user->email ?? 'N/A',
                'phone' => $booking->user->phone ?? 'N/A',
            ],
            'car' => [
                'name' => ($booking->car->brand->name ?? 'Unknown') . ' ' . ($booking->car->model ?? ''),
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