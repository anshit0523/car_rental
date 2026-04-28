<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\IssueStatus;
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
            'returnIssue',
        ]);

        if ($request->filled('status')) {
            $paymentsQuery->whereHas('paymentStatus', function ($query) use ($request) {
                $query->whereRaw('LOWER(name) = ?', [strtolower($request->status)]);
            });
        }

        if ($request->filled('payment_type')) {
            if ($request->payment_type === 'booking') {
                $paymentsQuery->whereNull('return_issue_id');
            }

            if ($request->payment_type === 'issue') {
                $paymentsQuery->whereNotNull('return_issue_id');
            }
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
            ? Payment::where('payment_status_id', $completedStatusId)->sum('amount')
            : 0;

        $thisMonth = $completedStatusId
            ? Payment::where('payment_status_id', $completedStatusId)
                ->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount')
            : 0;

        $lastMonth = $completedStatusId
            ? Payment::where('payment_status_id', $completedStatusId)
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
            ? Payment::where('payment_status_id', $completedStatusId)->count()
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
        DB::transaction(function () use ($payment) {
            $completedPaymentStatus = PaymentStatus::where('name', 'Completed')->firstOrFail();

            $payment->update([
                'payment_status_id' => $completedPaymentStatus->id,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $booking = $payment->booking;

            if (! $booking) {
                return;
            }

            if ($payment->return_issue_id && $payment->returnIssue) {
                $resolvedIssueStatus = IssueStatus::where('name', 'resolved')->first();

                if ($resolvedIssueStatus) {
                    $payment->returnIssue->update([
                        'issue_status_id' => $resolvedIssueStatus->id,
                    ]);
                }

                $completedBookingStatus = Status::where('name', 'Completed')->first();

                if ($completedBookingStatus) {
                    $booking->update([
                        'status_id' => $completedBookingStatus->id,
                    ]);
                }
            } else {
                $confirmedBookingStatus = Status::where('name', 'Confirmed')->first();

                if ($confirmedBookingStatus) {
                    $booking->update([
                        'status_id' => $confirmedBookingStatus->id,
                    ]);
                }
            }
        });

        return back()->with('success', 'Payment approved successfully.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_note' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $payment) {
            $failedPaymentStatus = PaymentStatus::where('name', 'Failed')->firstOrFail();

            $payment->update([
                'payment_status_id' => $failedPaymentStatus->id,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            if ($payment->booking && $payment->booking->photoReceipt) {
                $payment->booking->photoReceipt->update([
                    'admin_note' => $request->admin_note,
                ]);
            }

            if (! $payment->return_issue_id && $payment->booking) {
                $failedBookingStatus = Status::where('name', 'Failed')->first();

                if ($failedBookingStatus) {
                    $payment->booking->update([
                        'status_id' => $failedBookingStatus->id,
                    ]);
                }
            }
        });

        return back()->with('success', 'Payment rejected successfully.');
    }
}