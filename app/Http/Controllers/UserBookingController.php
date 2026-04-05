<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use App\Models\PaymentSetting;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserBookingController extends Controller
{

    /*** Show car details page for booking */
    public function show($carId)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission'])->findOrFail($carId);
        $serviceTypes = DB::table('service_types')->orderBy('id')->get();

        return view('user.usercardetails', [
            'car' => $car,
            'serviceTypes' => $serviceTypes

        ]);
    }


    /**
     * Store a new booking
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'car_id' => 'required|exists:cars,id',
        'pickup_date' => 'required|date|after_or_equal:today',
        'pickup_time' => 'required|date_format:H:i',
        'return_date' => 'required|date|after:pickup_date',
        'return_time' => 'required|date_format:H:i',
        'total_price' => 'required|numeric|min:0',
        'service_type_id' => 'required|exists:service_types,id',
        'service_location' => 'nullable|string|max:255',
        'phone' => 'required|string|max:30',
        'points_to_use' => 'nullable|integer|min:0',
    ]);

    $user = auth()->user();

    if (!$user->phone || $user->phone !== $request->input('phone')) {
        $user->update(['phone' => $request->input('phone')]);
    }

    $serviceTypeName = DB::table('service_types')
        ->where('id', $validated['service_type_id'])
        ->value('name');

    if (stripos($serviceTypeName, 'deliver') !== false && empty($validated['service_location'])) {
        return back()
            ->withErrors(['service_location' => 'Location is required for Delivery.'])
            ->withInput();
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

        $pendingStatus = Status::firstOrCreate(['name' => 'Pending']);

        $POINTS_PER_PESO = 10;
        $pointsRequested = (int) ($validated['points_to_use'] ?? 0);

        $booking = DB::transaction(function () use (
            $user,
            $validated,
            $pickupDateTime,
            $returnDateTime,
            $pendingStatus,
            $POINTS_PER_PESO,
            $pointsRequested
        ) {
            // Lock user row for safe points balance update
            $userRow = DB::table('users')
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();

            $balanceBefore = (int) $userRow->points_balance;

            // Re-check availability INSIDE transaction
            $existingBooking = Booking::where('car_id', $validated['car_id'])
                ->whereIn('status_id', [1, 2, 6])
                ->where(function ($query) use ($pickupDateTime, $returnDateTime) {
                    $query->where('pickup_at', '<', $returnDateTime)
                        ->where('return_at', '>', $pickupDateTime);
                })
                ->lockForUpdate()
                ->exists();

            if ($existingBooking) {
                throw new \RuntimeException('This car is not available for the selected dates.');
            }

            $pointsUsed = min($pointsRequested, $balanceBefore);
            $discountAmount = $pointsUsed / $POINTS_PER_PESO;

            $totalPrice = (float) $validated['total_price'];
            $finalTotal = max(0, $totalPrice - $discountAmount);

            $balanceAfter = $balanceBefore - $pointsUsed;

            DB::table('users')->where('id', $user->id)->update([
                'points_balance' => $balanceAfter,
                'updated_at' => now(),
            ]);

            $booking = Booking::create([
                'car_id' => $validated['car_id'],
                'user_id' => $user->id,
                'pickup_at' => $pickupDateTime,
                'return_at' => $returnDateTime,
                'total_price' => $totalPrice,
                'final_total' => $finalTotal,
                'points_used' => $pointsUsed,
                'discount_amount' => $discountAmount,
                'status_id' => $pendingStatus->id,
                'service_type_id' => $validated['service_type_id'],
                'service_location' => $validated['service_location'] ?? null,
            ]);

            if ($pointsUsed > 0) {
                $redeemTypeId = (int) DB::table('points_transaction_types')
                    ->where('name', 'redeem')
                    ->value('id');

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

    } catch (\RuntimeException $e) {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_type' => 'unavailable_dates',
            ], 422);
        }

        return back()
            ->withErrors(['booking' => $e->getMessage()])
            ->withInput();

    } catch (\Exception $e) {
        \Log::error('Booking error: ' . $e->getMessage());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating booking.',
                'error_type' => 'general_error',
            ], 500);
        }

        return back()
            ->withErrors(['booking' => 'Error creating booking. Please try again.'])
            ->withInput();
    }
}

    public function confirmation($id)
    {
        $booking = Booking::with(['car', 'user', 'payments', 'receipts'])->findOrFail($id);

        // Authorization check
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

    
    /**
     * Show payment page for a booking
     */
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

    $paymentSetting = PaymentSetting::first();

    return view('user.userpayments', [
        'booking' => $booking,
        'paymentSetting' => $paymentSetting,
    ]);
}

     public function cancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Authorization check
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Cannot cancel within 24 hours
        if ($booking->pickup_at <= Carbon::now()->addHours(24)) {
            return back()->withErrors(['booking' => 'Cannot cancel within 24 hours of pickup.']);
        }

        $cancelledStatus = Status::firstOrCreate(['name' => 'Cancelled']);
        $booking->update(['status_id' => $cancelledStatus->id]);

        return back()->with('success', 'Booking cancelled successfully.');
    }


    /**
     * Get car details for booking modal/AJAX
     */
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
}
