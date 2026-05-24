<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserBookingApiController extends Controller
{
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

        $user = $request->user();

        $pickupDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['pickup_date'] . ' ' . $validated['pickup_time']
        );

        $returnDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['return_date'] . ' ' . $validated['return_time']
        );

        if ($returnDateTime <= $pickupDateTime) {
            return response()->json([
                'success' => false,
                'message' => 'Return date/time must be after pickup date/time.',
            ], 422);
        }

        $serviceTypeName = DB::table('service_types')
            ->where('id', $validated['service_type_id'])
            ->value('name');

        if (stripos($serviceTypeName, 'deliver') !== false && empty($validated['service_location'])) {
            return response()->json([
                'success' => false,
                'message' => 'Location is required for delivery.',
            ], 422);
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
            return response()->json([
                'success' => false,
                'message' => 'This car is not available for the selected dates.',
            ], 422);
        }

        $pendingStatus = Status::firstOrCreate(['name' => 'Pending Payment']);
        $pointsPerPeso = 10;

        $booking = DB::transaction(function () use (
            $user,
            $validated,
            $pendingStatus,
            $pickupDateTime,
            $returnDateTime,
            $pointsPerPeso
        ) {
            $userRow = DB::table('users')
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();

            $balanceBefore = (int) ($userRow->points_balance ?? 0);
            $pointsRequested = (int) ($validated['points_to_use'] ?? 0);
            $totalPrice = (float) $validated['total_price'];

            $maxUsablePointsByTotal = (int) floor($totalPrice * $pointsPerPeso);

            $pointsUsed = min(
                $pointsRequested,
                $balanceBefore,
                $maxUsablePointsByTotal
            );

            $discountAmount = $pointsUsed / $pointsPerPeso;
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
                'notes' => 'Awaiting payment from mobile app booking.',
            ]);

            if ($pointsUsed > 0) {
                DB::table('points_transactions')->insert([
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'points_id' => 2,
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

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully.',
            'booking' => $booking,
            'payment' => [
                'booking_id' => $booking->id,
                'amount' => $booking->final_total,
            ],
        ], 201);
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

        $payment = DB::table('payments')
            ->leftJoin('payment_statuses', 'payments.payment_status_id', '=', 'payment_statuses.id')
            ->where('payments.booking_id', $booking->id)
            ->whereNull('payments.return_issue_id')
            ->select(
                'payments.id',
                'payments.payment_status_id',
                'payments.verified_at',
                'payment_statuses.name as payment_status_name',
                'payment_statuses.code as payment_status_code'
            )
            ->first();

        $paymentStatusName = strtolower(trim($payment->payment_status_name ?? ''));
        $paymentStatusCode = strtolower(trim($payment->payment_status_code ?? ''));

        if (
            $payment &&
            (
                $paymentStatusName === 'verified' ||
                $paymentStatusCode === 'verified' ||
                !empty($payment->verified_at)
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Booking can no longer be cancelled because payment is already verified by admin.',
            ], 422);
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

        if (!in_array($bookingStatus, $allowedStatuses, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending payment or pending verification bookings can be cancelled.',
            ], 422);
        }

        DB::transaction(function () use ($request, $booking) {
            $cancelledBookingStatus = Status::firstOrCreate(['name' => 'Cancelled']);

            $cancelledPaymentStatus = PaymentStatus::firstOrCreate(
                ['code' => 'cancelled'],
                ['name' => 'Cancelled']
            );

            DB::table('payments')
                ->where('booking_id', $booking->id)
                ->whereNull('return_issue_id')
                ->update([
                    'payment_status_id' => $cancelledPaymentStatus->id,
                    'verified_by' => $request->user()->id,
                    'verified_at' => now(),
                    'notes' => DB::raw("CONCAT(COALESCE(notes, ''), '\nCancelled by customer.')"),
                    'updated_at' => now(),
                ]);

            $pointsUsed = (int) ($booking->points_used ?? 0);

            if ($pointsUsed > 0) {
                $user = $booking->user()->lockForUpdate()->first();

                $balanceBefore = (int) ($user->points_balance ?? 0);
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
                'status_id' => $cancelledBookingStatus->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully. Payment status synced and points were returned if used.',
            'booking' => $booking->fresh(['status', 'user']),
        ]);
    }
}