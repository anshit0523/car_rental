<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\PaymentStatus;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserBookingController extends Controller
{
    /**
     * Show car details page for booking
     */
    public function show($carId)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission'])->findOrFail($carId);
        $serviceTypes = DB::table('service_types')->orderBy('id')->get();

        return view('user.usercardetails', [
            'car' => $car,
            'serviceTypes' => $serviceTypes,
        ]);
    }

    /**
     * Guest can fill the booking form.
     * If not logged in yet, keep the booking data in session,
     * then redirect to login/register.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'car_id' => 'required|exists:cars,id',
        'pickup_date' => 'required|date|after_or_equal:today',
        'pickup_time' => 'required|date_format:H:i',
        'return_date' => 'required|date|after_or_equal:pickup_date',
        'return_time' => 'required|date_format:H:i',
        'total_price' => 'required|numeric|min:0',
        'service_type_id' => 'required|exists:service_types,id',
        'service_location' => 'nullable|string|max:255',
        'phone' => 'required|string|max:30',
        'points_to_use' => 'nullable|integer|min:0',
    ]);

    $serviceTypeName = DB::table('service_types')
        ->where('id', $validated['service_type_id'])
        ->value('name');

    if (stripos($serviceTypeName, 'deliver') !== false && empty($validated['service_location'])) {
        return back()
            ->withErrors(['service_location' => 'Location is required for Delivery.'])
            ->withInput();
    }

    if (!auth()->check()) {
        $validated['points_to_use'] = 0;

        session([
            'guest_booking_payload' => $validated,
        ]);

        return redirect()
            ->route('login')
            ->with('info', 'Please sign in or create an account to continue to payment.');
    }

    try {
        $pickupDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['pickup_date'] . ' ' . $validated['pickup_time']
        );

        $returnDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['return_date'] . ' ' . $validated['return_time']
        );

        if ($returnDateTime <= $pickupDateTime) {
            return back()
                ->withErrors(['return_date' => 'Return date/time must be after pickup date/time.'])
                ->withInput();
        }

        $blockingStatusIds = Status::whereIn('name', [
            'Pending Payment',
            'Pending Payment Verification',
            'Confirmed',
            'Active',
            'Reserved',
            'Return',
        ])->pluck('id')->toArray();

        $existingBooking = Booking::where('car_id', $validated['car_id'])
            ->whereIn('status_id', $blockingStatusIds)
            ->where(function ($query) use ($pickupDateTime, $returnDateTime) {
                $query->where('pickup_at', '<', $returnDateTime)
                    ->where('return_at', '>', $pickupDateTime);
            })
            ->exists();

        if ($existingBooking) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This car is not available for the selected dates.',
                    'error_type' => 'unavailable_dates',
                ], 422);
            }

            return back()
                ->withErrors(['booking' => 'This car is not available for the selected dates.'])
                ->withInput();
        }

        $pendingStatus = Status::firstOrCreate(['name' => 'Pending Payment']);

        $booking = $this->createBookingForUser(
            auth()->user(),
            $validated,
            $pendingStatus,
            $pickupDateTime,
            $returnDateTime
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'redirect' => route('user.payments', ['booking_id' => $booking->id]),
            ]);
        }

        return redirect()
            ->route('user.payments', ['booking_id' => $booking->id])
            ->with('success', 'Booking created successfully!');
    } catch (\Exception $e) {
        \Log::error('Booking error: ' . $e->getMessage());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating booking: ' . $e->getMessage(),
                'error_type' => 'general_error',
            ], 500);
        }

        return back()
            ->withErrors(['booking' => 'Error creating booking: ' . $e->getMessage()])
            ->withInput();
    }
}
    /**
     * Called right after login/register.
     * Creates the real booking from the guest session,
     * then redirects directly to payment page.
     */
   public function continueGuestBooking()
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $payload = session('guest_booking_payload');

    if (!$payload) {
        return redirect()->route('user.browse')
            ->with('error', 'No pending booking found. Please select a car again.');
    }

    try {
        $pickupDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $payload['pickup_date'] . ' ' . $payload['pickup_time']
        );

        $returnDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $payload['return_date'] . ' ' . $payload['return_time']
        );

        $pendingStatus = Status::firstOrCreate(['name' => 'Pending Payment']);

        $booking = $this->createBookingForUser(
            auth()->user(),
            $payload,
            $pendingStatus,
            $pickupDateTime,
            $returnDateTime
        );

        session()->forget('guest_booking_payload');

        return redirect()
            ->route('user.payments', ['booking_id' => $booking->id])
            ->with('success', 'Booking created successfully. Please continue with payment.');
    } catch (\RuntimeException $e) {
        session()->forget('guest_booking_payload');

        return redirect()
            ->route('user.browse')
            ->withErrors(['booking' => $e->getMessage()]);
    } catch (\Exception $e) {
        \Log::error('Continue guest booking error: ' . $e->getMessage());

        session()->forget('guest_booking_payload');

        return redirect()
            ->route('user.browse')
            ->withErrors(['booking' => 'Error creating booking. Please try again.']);
    }
}

    /**
     * Create a brand-new booking when the previous booking was rejected/failed.
     * This avoids reusing the same booking ID.
     */
    public function retryRejectedBooking($bookingId)
    {
        $oldBooking = Booking::with(['status', 'car'])->findOrFail($bookingId);

        if ($oldBooking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (($oldBooking->status->name ?? '') !== 'Failed') {
            return back()->withErrors([
                'booking' => 'Only rejected bookings can be retried.'
            ]);
        }

        $blockingStatusIds = Status::whereIn('name', [
            'Pending',
            'Pending Payment Verification',
            'Confirmed',
            'Active',
            'Reserved',
        ])->pluck('id')->toArray();

        $hasConflict = Booking::where('car_id', $oldBooking->car_id)
            ->whereIn('status_id', $blockingStatusIds)
            ->where('id', '!=', $oldBooking->id)
            ->where(function ($query) use ($oldBooking) {
                $query->where('pickup_at', '<', $oldBooking->return_at)
                    ->where('return_at', '>', $oldBooking->pickup_at);
            })
            ->exists();

        if ($hasConflict) {
            return redirect()
                ->route('user.rentals.failed')
                ->withErrors([
                    'booking' => 'This car is no longer available for the same schedule. Please create a new booking with a different schedule.'
                ]);
        }

        $pendingStatus = Status::firstOrCreate(['name' => 'Pending']);

        $newBooking = DB::transaction(function () use ($oldBooking, $pendingStatus) {
            return Booking::create([
                'car_id' => $oldBooking->car_id,
                'user_id' => auth()->id(),
                'pickup_at' => $oldBooking->pickup_at,
                'return_at' => $oldBooking->return_at,
                'total_price' => $oldBooking->total_price,
                'final_total' => $oldBooking->final_total ?? $oldBooking->total_price,
                'status_id' => $pendingStatus->id,
                'service_type_id' => $oldBooking->service_type_id,
                'service_location' => $oldBooking->service_location,
            ]);
        });

        return redirect()
            ->route('user.payments', ['booking_id' => $newBooking->id])
            ->with('success', 'A new booking has been created. Please upload your new payment receipt.');
    }

    /**
     * Shared booking creation logic for logged-in users and guest-then-login flow.
     */
   private function createBookingForUser(
    $user,
    array $validated,
    Status $pendingStatus,
    Carbon $pickupDateTime,
    Carbon $returnDateTime
): Booking {
    $POINTS_PER_PESO = 10;

    return DB::transaction(function () use (
        $user,
        $validated,
        $pendingStatus,
        $pickupDateTime,
        $returnDateTime,
        $POINTS_PER_PESO
    ) {
        $userRow = DB::table('users')
            ->where('id', $user->id)
            ->lockForUpdate()
            ->first();

        $balanceBefore = (int) ($userRow->points_balance ?? 0);
        $pointsRequested = (int) ($validated['points_to_use'] ?? 0);
        $totalPrice = (float) $validated['total_price'];

        $maxUsablePointsByTotal = (int) floor($totalPrice * $POINTS_PER_PESO);

        $pointsUsed = min(
            $pointsRequested,
            $balanceBefore,
            $maxUsablePointsByTotal
        );

        $discountAmount = $pointsUsed / $POINTS_PER_PESO;
        $finalTotal = max(0, $totalPrice - $discountAmount);
        $balanceAfter = $balanceBefore - $pointsUsed;

        if ($pointsUsed > 0) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'points_balance' => $balanceAfter,
                    'updated_at' => now(),
                ]);
        }

        $booking = Booking::create([
            'car_id' => $validated['car_id'],
            'user_id' => $user->id,
            'pickup_at' => $pickupDateTime,
            'return_at' => $returnDateTime,
            'total_price' => $totalPrice,
            'points_used' => $pointsUsed,
            'discount_amount' => $discountAmount,
            'final_total' => $finalTotal,
            'status_id' => $pendingStatus->id,
            'service_type_id' => $validated['service_type_id'],
            'service_location' => $validated['service_location'] ?? null,
        ]);

        $awaitingPaymentStatus = PaymentStatus::firstOrCreate(
            ['code' => 'awaiting_payment'],
            ['name' => 'Awaiting Payment']
        );

        Payment::create([
            'booking_id' => $booking->id,
            'payment_date' => now(),
            'amount' => $finalTotal,
            'payment_method_id' => null,
            'payment_status_id' => $awaitingPaymentStatus->id,
            'transaction_id' => null,
            'notes' => 'Awaiting customer payment submission',
        ]);

        if ($pointsUsed > 0) {
            $redeemTypeId = DB::table('points_transaction_types')
                ->where('name', 'redeem')
                ->value('id');

            if (! $redeemTypeId) {
                $redeemTypeId = DB::table('points_transaction_types')->insertGetId([
                    'name' => 'redeem',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('points_transactions')->insert([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'points_id' => $redeemTypeId,
                'points_change' => -$pointsUsed,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'note' => 'Redeemed points for booking',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $booking;
    });
}
    public function confirmation($id)
    {
        $booking = Booking::with(['car', 'user', 'payments', 'receipts'])->findOrFail($id);

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $payment = $booking->payments()->latest()->first();
        $receipt = $booking->receipts()->latest()->first();

        return view('user.booking-confirmation', [
            'booking' => $booking,
            'payment' => $payment,
            'receipt' => $receipt,
        ]);
    }

    public function showPayment()
    {
        $bookingId = request()->query('booking_id');

        if (!$bookingId) {
            return redirect()->route('user.browse')
                ->withErrors('Booking ID is required. Please complete your booking.');
        }

        $booking = Booking::with(['car', 'user', 'status'])->find($bookingId);

        if (!$booking) {
            return redirect()->route('user.browse')
                ->withErrors('Booking not found.');
        }

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (($booking->status->name ?? '') === 'Failed') {
            return redirect()->route('user.rentals.failed')
                ->withErrors('This rejected booking cannot be paid again. Please create a new booking.');
        }

        $paymentSetting = PaymentSetting::first();

        return view('user.userpayments', [
            'booking' => $booking,
            'paymentSetting' => $paymentSetting,
        ]);
    }

public function cancel($bookingId)
{
    $booking = Booking::with(['status', 'user'])->findOrFail($bookingId);

    if ($booking->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }

    if ($booking->created_at <= Carbon::now()->subHours(24)) {
        return back()->withErrors([
            'booking' => 'Cannot cancel after 24 hours from booking creation.'
        ]);
    }

    $bookingStatus = strtolower(trim($booking->status->name ?? ''));

    $allowedStatuses = [
        'pending payment',
        'pending payment verification',
    ];

    if (!in_array($bookingStatus, $allowedStatuses)) {
        return back()->withErrors([
            'booking' => 'Only pending payment or pending verification bookings can be cancelled by customer.'
        ]);
    }

    DB::transaction(function () use ($booking, $bookingStatus) {
        $cancelledStatus = Status::firstOrCreate(['name' => 'Cancelled']);

        if ($bookingStatus === 'pending payment verification') {
            $cancelledPaymentStatusId = DB::table('payment_statuses')
                ->where('name', 'Cancelled')
                ->value('id');

            if ($cancelledPaymentStatusId) {
                DB::table('payments')
                    ->where('booking_id', $booking->id)
                    ->update([
                        'payment_status_id' => $cancelledPaymentStatusId,
                        'verified_by' => auth()->id(),
                        'verified_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        }

        $pointsUsed = (int) ($booking->points_used ?? 0);

        if ($pointsUsed > 0) {
            $user = $booking->user;

            $balanceBefore = (int) $user->points_balance;
            $balanceAfter = $balanceBefore + $pointsUsed;

            DB::table('points_transactions')->insert([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'points_id' => 2,
                'points_change' => $pointsUsed,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'note' => 'Returned points after customer cancelled booking within 24 hours.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $user->update([
                'points_balance' => $balanceAfter,
            ]);
        }

        $booking->update([
            'status_id' => $cancelledStatus->id,
        ]);
    });

    return back()->with('success', 'Booking cancelled successfully. Points were returned if used.');
}

    public function myBookings()
    {
        $bookings = Booking::with(['car', 'status', 'payments'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.userrentals', compact('bookings'));
    }

    public function getCarDetails($carId)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission'])->findOrFail($carId);

        return response()->json([
            'id' => $car->id,
            'name' => $car->brand->name . ' ' . $car->model,
            'price_per_day' => $car->price_per_day,
            'seats' => $car->seats,
            'fuel_type' => $car->fuelType->type,
            'transmission' => $car->transmission->type,
        ]);
    }



    public function cancel(Request $request, Booking $booking)
{
    $booking->load(['status', 'user']);

    if ($booking->user_id !== $request->user()->id) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.',
        ], 403);
    }

    if ($booking->created_at <= Carbon::now()->subHours(24)) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot cancel after 24 hours from booking creation.',
        ], 422);
    }

    $bookingStatus = strtolower(trim($booking->status->name ?? ''));

    $allowedStatuses = [
        'pending payment',
        'pending payment verification',
    ];

    if (!in_array($bookingStatus, $allowedStatuses)) {
        return response()->json([
            'success' => false,
            'message' => 'Only pending payment or pending verification bookings can be cancelled by customer.',
        ], 422);
    }

    DB::transaction(function () use ($booking, $bookingStatus, $request) {
        $cancelledStatus = Status::firstOrCreate(['name' => 'Cancelled']);

        if ($bookingStatus === 'pending payment verification') {
            $cancelledPaymentStatusId = DB::table('payment_statuses')
                ->where('name', 'Cancelled')
                ->value('id');

            if ($cancelledPaymentStatusId) {
                DB::table('payments')
                    ->where('booking_id', $booking->id)
                    ->update([
                        'payment_status_id' => $cancelledPaymentStatusId,
                        'verified_by' => $request->user()->id,
                        'verified_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        }

        $pointsUsed = (int) ($booking->points_used ?? 0);

        if ($pointsUsed > 0) {
            $user = $booking->user;

            $balanceBefore = (int) $user->points_balance;
            $balanceAfter = $balanceBefore + $pointsUsed;

            DB::table('points_transactions')->insert([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'points_id' => 2,
                'points_change' => $pointsUsed,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'note' => 'Returned points after customer cancelled booking within 24 hours.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $user->update([
                'points_balance' => $balanceAfter,
            ]);
        }

        $booking->update([
            'status_id' => $cancelledStatus->id,
        ]);
    });

    return response()->json([
        'success' => true,
        'message' => 'Booking cancelled successfully. Points were returned if used.',
        'booking' => $this->formatBooking($booking->fresh(['car.brand', 'car.fuelType', 'car.transmission', 'status', 'photoReceipt'])),
    ]);
}

}