<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentMethods;
use App\Models\PaymentStatus;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'car.brand', 'status']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->orWhereHas('car', function ($c) use ($search) {
                    $c->where('model', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $bookings = $query->latest()->paginate(10)->onEachSide(1);
        $statuses = Status::all();

        return view('admin.adminbooking', compact('bookings', 'statuses'));
    }

    public function create(Car $car)
    {
        return redirect()
            ->route('admin.calendar')
            ->with('info', 'Use the available slot in the calendar to create a walk-in booking for ' . ($car->brand->name ?? 'selected car') . ' ' . $car->model . '.');
    }

public function searchCustomer(Request $request): JsonResponse
{
    $request->validate([
        'keyword' => 'required|string|min:1|max:255',
    ]);

    $keyword = trim($request->keyword);
    $keywordLower = strtolower($keyword);

    $users = User::query()
        ->where('role_id', 2)
        ->where(function ($q) use ($keyword, $keywordLower) {
            $q->whereRaw('LOWER(name) LIKE ?', ["{$keywordLower}%"])
              ->orWhereRaw('LOWER(name) LIKE ?', ["% {$keywordLower}%"])
              ->orWhereRaw('LOWER(email) LIKE ?', ["{$keywordLower}%"])
              ->orWhereRaw('LOWER(email) LIKE ?', ["%{$keywordLower}%"])
              ->orWhere('phone', 'like', "{$keyword}%")
              ->orWhere('phone', 'like', "%{$keyword}%");
        })
        ->orderBy('name')
        ->limit(10)
        ->get(['id', 'name', 'email', 'phone']);

    if ($users->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Customer not found.',
            'users' => [],
        ], 404);
    }

    return response()->json([
        'success' => true,
        'users' => $users,
    ]);
}



public function store(Request $request)
{
    $baseRules = [
        'car_id' => 'required|exists:cars,id',
        'pickup_date' => 'required|date',
        'pickup_time' => 'required|date_format:H:i',
        'return_date' => 'required|date',
        'return_time' => 'required|date_format:H:i',
        'service_type_id' => 'required|exists:service_types,id',
        'payment_method_code' => 'required|exists:payment_methods,code',
        'customer_type' => 'required|in:existing,new',
        'existing_user_id' => 'nullable|exists:users,id',
        'existing_customer_search' => 'nullable|string|max:255',
        'customer_name' => 'nullable|string|max:255',
        'customer_email' => 'nullable|email|max:255',
        'customer_phone' => 'nullable|string|max:30',
        'password' => 'nullable|string|min:6|confirmed',
        'service_location' => 'nullable|string|max:255',
    ];

    $validated = $request->validate($baseRules);

    if ($validated['customer_type'] === 'existing') {
        $request->validate([
            'existing_user_id' => 'required|exists:users,id',
        ]);
    } else {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255|unique:users,email',
            'customer_phone' => 'required|string|max:30',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    try {
        $pickupAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['pickup_date'] . ' ' . $validated['pickup_time']
        );

        $returnAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['return_date'] . ' ' . $validated['return_time']
        );

        if ($returnAt->lte($pickupAt)) {
            return $this->bookingErrorResponse(
                $request,
                'Return date/time must be after pickup date/time.',
                422
            );
        }

        $car = Car::findOrFail($validated['car_id']);

        $serviceTypeName = DB::table('service_types')
            ->where('id', $validated['service_type_id'])
            ->value('name');

        if (
            stripos((string) $serviceTypeName, 'deliver') !== false &&
            empty($validated['service_location'])
        ) {
            return $this->bookingErrorResponse(
                $request,
                'Location is required for delivery bookings.',
                422
            );
        }

        $blockingStatusIds = Status::whereIn('name', [
            'Pending',
            'Confirmed',
            'Reserved',
            'Approved',
            'Active',
            'Pending Payment Verification',
        ])->pluck('id')->toArray();

        $hasConflict = Booking::where('car_id', $car->id)
            ->whereIn('status_id', $blockingStatusIds)
            ->where(function ($query) use ($pickupAt, $returnAt) {
                $query->where('pickup_at', '<', $returnAt)
                    ->where('return_at', '>', $pickupAt);
            })
            ->exists();

        if ($hasConflict) {
            return $this->bookingErrorResponse(
                $request,
                'This car is not available for the selected date and time.',
                422
            );
        }

        $paymentMethod = PaymentMethods::where('code', $validated['payment_method_code'])->firstOrFail();
        $isCash = $paymentMethod->code === 'cash';

        $bookingStatus = Status::firstOrCreate([
            'name' => $isCash ? 'Confirmed' : 'Pending Payment Verification',
        ]);

        $paymentStatus = PaymentStatus::where('code', $isCash ? 'completed' : 'pending')->firstOrFail();

        $days = max(1, (int) ceil($pickupAt->diffInMinutes($returnAt) / 1440));
        $total = (float) $car->price_per_day * $days;

        $booking = DB::transaction(function () use (
            $validated,
            $pickupAt,
            $returnAt,
            $car,
            $total,
            $paymentMethod,
            $bookingStatus,
            $paymentStatus,
            $isCash
        ) {
            if ($validated['customer_type'] === 'existing') {
                $user = User::where('role_id', 2)->findOrFail($validated['existing_user_id']);
            } else {
                $user = User::create([
                    'name' => $validated['customer_name'],
                    'email' => strtolower(trim($validated['customer_email'])),
                    'phone' => $validated['customer_phone'],
                    'password' => Hash::make($validated['password']),
                    'role_id' => 2,
                ]);
            }

            $booking = Booking::create([
                'car_id' => $car->id,
                'user_id' => $user->id,
                'pickup_at' => $pickupAt,
                'return_at' => $returnAt,
                'total_price' => $total,
                'final_total' => $total,
                'status_id' => $bookingStatus->id,
                'service_type_id' => $validated['service_type_id'],
                'service_location' => $validated['service_location'] ?? null,
            ]);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_date' => now(),
                'amount' => $booking->final_total ?? $booking->total_price ?? 0,
                'payment_method_id' => $paymentMethod->id,
                'payment_status_id' => $paymentStatus->id,
                'transaction_id' => null,
                'notes' => $isCash
                    ? 'Cash payment completed during booking creation'
                    : 'Payment submitted and waiting for verification',
                    'verified_by' => $isCash ? auth()->id() : null,
    'verified_at' => $isCash ? now() : null,
            ]);

            \Log::info('Admin booking payment created', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method_id' => $payment->payment_method_id,
                'payment_status_id' => $payment->payment_status_id,
            ]);

            return $booking;
        });

        return $this->bookingSuccessResponse(
            $request,
            'Walk-in booking created successfully.',
            $booking->id
        );
    } catch (\Throwable $e) {
        \Log::error('Admin walk-in booking error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return $this->bookingErrorResponse(
            $request,
            'Failed to create booking.',
            500
        );
    }
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

    public function cancel($id)
    {
        $booking = Booking::with(['status', 'user'])->findOrFail($id);

        if (($booking->status->name ?? '') === 'Cancelled') {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', 'Booking is already cancelled.');
        }

        $cancelledStatus = Status::firstOrCreate(['name' => 'Cancelled']);

        $booking->update([
            'status_id' => $cancelledStatus->id,
        ]);

        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'title' => 'Booking Cancelled',
            'message' => 'Your booking has been cancelled by the administrator.',
            'type' => 'booking',
            'link' => route('user.booking.confirmation', $booking->id),
        ]);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    public function calendar(Request $request)
    {
        $view = $request->get('view', 'weekly');
        $dateString = $request->get('date');

        $startDate = $dateString
            ? Carbon::parse($dateString)
            : now();

        $startDate->startOfDay();

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

        $query = Car::with([
            'brand:id,name',
            'transmission:id,type',
            'fuelType',
            'bookings' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('status', function ($s) {
                    $s->whereIn('name', [
                        'Pending',
                        'Confirmed',
                        'Reserved',
                        'Approved',
                        'Active',
                        'Pending Payment Verification',
                    ]);
                })
                ->where(function ($date) use ($startDate, $endDate) {
                    $date->whereBetween('pickup_at', [$startDate, $endDate])
                        ->orWhereBetween('return_at', [$startDate, $endDate])
                        ->orWhere(function ($overlap) use ($startDate, $endDate) {
                            $overlap->where('pickup_at', '<=', $startDate)
                                ->where('return_at', '>=', $endDate);
                        });
                })
                ->with('status')
                ->orderBy('pickup_at');
            }
        ])->where('active', true);

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->get('brand_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');

            $query->where(function ($q) use ($search) {
                $q->where('model', 'like', "%{$search}%");
            });
        }

        $cars = $query->paginate(5);

        $previousWeek = $startDate->copy()->subDays($view === '30days' ? 30 : 7)->format('Y-m-d');
        $nextWeek = $startDate->copy()->addDays($view === '30days' ? 30 : 7)->format('Y-m-d');

        $brands = Brand::all();
        $serviceTypes = DB::table('service_types')->orderBy('id')->get();
        $paymentMethods = PaymentMethods::orderBy('name')->get();

        $data = [
            'cars' => $cars,
            'calendarDates' => $calendarDates,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'previousWeek' => $previousWeek,
            'nextWeek' => $nextWeek,
            'brands' => $brands,
            'view' => $view,
            'serviceTypes' => $serviceTypes,
            'paymentMethods' => $paymentMethods,
        ];

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
                'pickup_at_iso' => $booking->pickup_at?->toIso8601String(),
                'return_at' => $booking->return_at?->format('M d, Y h:i A') ?? 'N/A',
                'return_at_iso' => $booking->return_at?->toIso8601String(),
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
                    'id' => $booking->car->id ?? null,
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

    private function bookingErrorResponse(Request $request, string $message, int $statusCode = 422)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $statusCode);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $message);
    }

    private function bookingSuccessResponse(Request $request, string $message, int $bookingId)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'booking_id' => $bookingId,
            ]);
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', $message);
    }
}