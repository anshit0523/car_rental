<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentMethods;
use App\Models\PaymentSetting;
use App\Models\PaymentStatus;
use App\Models\PhotoReceipt;
use App\Models\Status;
use Illuminate\Http\Request;

class UserPaymentApiController extends Controller
{
    public function index(Request $request)
    {
        $awaitingPaymentStatus = PaymentStatus::where('code', 'awaiting_payment')->firstOrFail();
        $pendingPaymentStatus = Status::where('name', 'Pending Payment')->firstOrFail();

        $payments = Payment::with([
                'booking.car.brand',
                'booking.car.transmission',
                'booking.car.fuelType',
                'booking.status',
                'paymentStatus',
            ])
            ->whereNull('return_issue_id')
            ->where('payment_status_id', $awaitingPaymentStatus->id)
            ->whereHas('booking', function ($query) use ($request, $pendingPaymentStatus) {
                $query->where('user_id', $request->user()->id)
                    ->where('status_id', $pendingPaymentStatus->id);
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'payment_setting' => PaymentSetting::first(),
        ]);
    }

    public function uploadReceipt(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|in:gcash,bank',
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $booking = Booking::with(['status', 'user'])->findOrFail($validated['booking_id']);

        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $paymentMethod = PaymentMethods::where('code', $validated['payment_method'])->firstOrFail();

        $pendingPaymentStatus = PaymentStatus::where('code', 'pending')->firstOrFail();

        $awaitingPaymentStatus = PaymentStatus::firstOrCreate(
            ['code' => 'awaiting_payment'],
            ['name' => 'Awaiting Payment']
        );

        $amount = (float) ($booking->final_total ?? $booking->total_price);
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

        $receiptPath = $request->file('receipt_image')->store('payment_receipts', $disk);

        $payment = Payment::where('booking_id', $booking->id)
            ->whereNull('return_issue_id')
            ->where('payment_status_id', $awaitingPaymentStatus->id)
            ->latest()
            ->first();

        if ($payment) {
            $payment->update([
                'payment_date' => now(),
                'amount' => $amount,
                'payment_method_id' => $paymentMethod->id,
                'payment_status_id' => $pendingPaymentStatus->id,
                'transaction_id' => null,
                'notes' => 'Manual booking payment submitted from mobile app',
            ]);
        } else {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'return_issue_id' => null,
                'payment_date' => now(),
                'amount' => $amount,
                'payment_method_id' => $paymentMethod->id,
                'payment_status_id' => $pendingPaymentStatus->id,
                'transaction_id' => null,
                'notes' => 'Manual booking payment submitted from mobile app',
            ]);
        }

        PhotoReceipt::updateOrCreate(
            [
                'booking_id' => $booking->id,
                'return_issue_id' => null,
            ],
            [
                'payment_id' => $payment->id,
                'user_id' => $request->user()->id,
                'image_path' => $receiptPath,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
            ]
        );

        $pendingBookingStatus = Status::firstOrCreate([
            'name' => 'Pending Payment Verification',
        ]);

        $booking->update([
            'status_id' => $pendingBookingStatus->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment receipt uploaded successfully.',
            'payment' => $payment,
        ]);
    }
}