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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        $paymentSetting = PaymentSetting::first();

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'payment_setting' => $this->formatPaymentSetting($paymentSetting),
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

    public function uploadReturnIssueReceipt(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'return_issue_id' => 'required|exists:return_issues,id',
            'receipt' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'payment_method' => 'required|in:gcash,bank',
        ]);

        $booking = Booking::with(['returnIssues.issueStatus'])
            ->findOrFail($validated['booking_id']);

        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $returnIssue = $booking->returnIssues
            ->where('id', $validated['return_issue_id'])
            ->first();

        if (!$returnIssue) {
            return response()->json([
                'success' => false,
                'message' => 'Return issue not found.',
            ], 404);
        }

        $statusName = strtolower($returnIssue->issueStatus?->name ?? '');

        if ($statusName !== 'awaiting_payment') {
            return response()->json([
                'success' => false,
                'message' => 'Return issue is not awaiting payment.',
            ], 422);
        }

        if ((float) $returnIssue->final_charge <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No payable final charge found for this return issue.',
            ], 422);
        }

        $paymentMethod = PaymentMethods::where('code', $validated['payment_method'])->first();

        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method.',
            ], 422);
        }

        $pendingStatus = PaymentStatus::where('name', 'Pending')->first();

        if (!$pendingStatus) {
            return response()->json([
                'success' => false,
                'message' => 'Pending payment status not found.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $booking, $returnIssue, $paymentMethod, $pendingStatus) {
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

            $path = $request->file('receipt')->store('payment-receipts', $disk);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'return_issue_id' => $returnIssue->id,
                'payment_date' => now(),
                'amount' => $returnIssue->final_charge,
                'payment_method_id' => $paymentMethod->id,
                'payment_status_id' => $pendingStatus->id,
                'transaction_id' => null,
                'notes' => 'Return issue payment uploaded from mobile.',
            ]);

            $receipt = PhotoReceipt::create([
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'user_id' => $request->user()->id,
                'return_issue_id' => $returnIssue->id,
                'image_path' => $path,
                'payment_method' => $paymentMethod->name,
                'status' => 'Pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Return issue receipt uploaded successfully.',
                'payment' => $payment,
                'receipt' => $receipt,
            ]);
        });
    }

    private function formatPaymentSetting(?PaymentSetting $paymentSetting): ?array
    {
        if (!$paymentSetting) {
            return null;
        }

        $gcashQrUrl = null;

        if ($paymentSetting->gcash_qr_image) {
            $cleanPath = ltrim(str_replace('storage/', '', $paymentSetting->gcash_qr_image), '/');

            $gcashQrUrl = config('filesystems.default') === 's3'
                ? Storage::disk('s3')->url($cleanPath)
                : asset('storage/' . $cleanPath);
        }

        return [
            'gcash_account_name' => $paymentSetting->gcash_account_name,
            'gcash_number' => $paymentSetting->gcash_number,
            'gcash_qr_image' => $paymentSetting->gcash_qr_image,
            'gcash_qr_url' => $gcashQrUrl,
            'bank_name' => $paymentSetting->bank_name,
            'bank_account_name' => $paymentSetting->bank_account_name,
            'bank_account_number' => $paymentSetting->bank_account_number,
        ];
    }
}
