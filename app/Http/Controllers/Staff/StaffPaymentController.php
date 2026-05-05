<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffPaymentController extends Controller
{
    public function index(Request $request)
    {
        $paymentsQuery = Payment::with([
                'booking.user',
                'booking.photoReceipt',
                'booking.status',
                'paymentMethod',
                'paymentStatus',
                'verifiedByUser',
            ])
            /*
            |--------------------------------------------------------------------------
            | Staff can only see normal booking payments.
            | Return issue payments are admin-only.
            |--------------------------------------------------------------------------
            */
            ->whereNull('return_issue_id');

        if ($request->filled('status')) {
            $paymentsQuery->whereHas('paymentStatus', function ($query) use ($request) {
                $query->whereRaw('LOWER(name) = ?', [strtolower($request->status)]);
            });
        }

        if ($request->filled('date_from')) {
            $paymentsQuery->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $paymentsQuery->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = (clone $paymentsQuery)
            ->latest('payment_date')
            ->paginate(10)
            ->withQueryString();

        $completedStatusId = PaymentStatus::where('name', 'Completed')->value('id');

        $totalReceived = $completedStatusId
            ? Payment::whereNull('return_issue_id')
                ->where('payment_status_id', $completedStatusId)
                ->sum('amount')
            : 0;

        $thisMonth = $completedStatusId
            ? Payment::whereNull('return_issue_id')
                ->where('payment_status_id', $completedStatusId)
                ->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount')
            : 0;

        $lastMonth = $completedStatusId
            ? Payment::whereNull('return_issue_id')
                ->where('payment_status_id', $completedStatusId)
                ->whereBetween('payment_date', [
                    now()->copy()->subMonth()->startOfMonth(),
                    now()->copy()->subMonth()->endOfMonth(),
                ])
                ->sum('amount')
            : 0;

        $monthlyGrowth = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : 0;

        $successfulPayments = $completedStatusId
            ? Payment::whereNull('return_issue_id')
                ->where('payment_status_id', $completedStatusId)
                ->count()
            : 0;

        return view('staff.staffpayment', [
            'payments' => $payments,
            'totalReceived' => $totalReceived,
            'thisMonth' => $thisMonth,
            'monthlyGrowth' => $monthlyGrowth,
            'successfulPayments' => $successfulPayments,
        ]);
    }

    public function approve(Payment $payment)
    {
        /*
        |--------------------------------------------------------------------------
        | Staff cannot approve return issue payments.
        |--------------------------------------------------------------------------
        */
        if ($payment->return_issue_id) {
            return back()->with('error', 'Staff are not allowed to approve return issue payments.');
        }

        DB::transaction(function () use ($payment) {
            $payment->load([
                'booking.user',
                'booking.photoReceipt',
            ]);

            $completedPaymentStatus = PaymentStatus::where('name', 'Completed')->firstOrFail();

            $payment->update([
                'payment_status_id' => $completedPaymentStatus->id,
                'payment_date' => now(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $booking = $payment->booking;

            if (! $booking) {
                return;
            }

            if ($booking->photoReceipt) {
                $booking->photoReceipt->update([
                    'status' => 'verified',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }

            $confirmedBookingStatus = Status::where('name', 'Confirmed')->first();

            if ($confirmedBookingStatus) {
                $booking->update([
                    'status_id' => $confirmedBookingStatus->id,
                ]);
            }

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'title' => 'Payment Approved',
                'message' => 'Your payment has been approved. Your booking is now confirmed.',
                'type' => 'payment_approved',
                'link' => route('user.booking.confirmation', $booking->id),
            ]);
        });

        return back()->with('success', 'Payment approved successfully.');
    }

    public function reject(Request $request, Payment $payment)
    {
        /*
        |--------------------------------------------------------------------------
        | Staff cannot reject return issue payments.
        |--------------------------------------------------------------------------
        */
        if ($payment->return_issue_id) {
            return back()->with('error', 'Staff are not allowed to reject return issue payments.');
        }

        $request->validate([
            'admin_note' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $payment) {
            $payment->load([
                'booking.user',
                'booking.photoReceipt',
            ]);

            $failedPaymentStatus = PaymentStatus::where('name', 'Failed')->firstOrFail();

            $payment->update([
                'payment_status_id' => $failedPaymentStatus->id,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $booking = $payment->booking;

            if (! $booking) {
                return;
            }

            if ($booking->photoReceipt) {
                $booking->photoReceipt->update([
                    'status' => 'rejected',
                    'admin_note' => $request->admin_note,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }

            $failedBookingStatus = Status::where('name', 'Failed')->first();

            if ($failedBookingStatus) {
                $booking->update([
                    'status_id' => $failedBookingStatus->id,
                ]);
            }

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'title' => 'Payment Rejected',
                'message' => 'Your payment receipt was rejected. Reason: ' . $request->admin_note,
                'type' => 'payment_rejected',
                'link' => route('user.rentals.failed'),
            ]);
        });

        return back()->with('success', 'Payment rejected successfully.');
    }
}