<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StaffCalendarController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'weekly');
        $dateString = $request->get('date');

        $startDate = $dateString ? Carbon::parse($dateString) : now();
        $startDate->startOfDay();

        $endDate = $view === '30days'
            ? $startDate->copy()->addDays(29)
            : $startDate->copy()->addDays(6);

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

        $query = Car::with([
            'brand:id,name',
            'transmission:id,type',
            'fuelType',
            'bookings' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('status', function ($s) {
                    $s->whereIn('name', ['Reserved', 'Active', 'Pending', 'Confirmed']);
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

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->get('brand_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('model', 'like', "%{$search}%");
        }

        $cars = $query->paginate(5);

        $previousWeek = $startDate->copy()->subDays($view === '30days' ? 30 : 7)->format('Y-m-d');
        $nextWeek = $startDate->copy()->addDays($view === '30days' ? 30 : 7)->format('Y-m-d');

        $brands = Brand::all();

        $data = compact(
            'cars',
            'calendarDates',
            'startDate',
            'endDate',
            'previousWeek',
            'nextWeek',
            'brands',
            'view'
        );

        if ($request->ajax()) {
            return view('staff.staffcalendar', $data)->render();
        }

        return view('staff.staffcalendar', $data);
    }

    public function showBookingJson(Booking $booking): JsonResponse
    {
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
    }
}