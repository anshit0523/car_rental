<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Payment;
use App\Models\Status;
use App\Models\IssueStatus;
use App\Models\ReturnIssueHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $completedStatusId = DB::table('payment_statuses')
            ->where('name', 'Completed')
            ->value('id');

        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $statsQuery = Payment::query();

        if ($status) {
            $statusId = DB::table('payment_statuses')
                ->where('name', ucfirst($status))
                ->value('id');

            $statsQuery->where('payment_status_id', $statusId);
        } else {
            $statsQuery->where('payment_status_id', $completedStatusId);
        }

        if ($dateFrom) {
            $statsQuery->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $statsQuery->whereDate('payment_date', '<=', $dateTo);
        }

        $totalReceived = (clone $statsQuery)->sum('amount') ?? 0;
        $successfulPayments = (clone $statsQuery)->count();

        $thisMonthQuery = clone $statsQuery;

        if (!$dateFrom && !$dateTo) {
            $thisMonthQuery->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year);
        }

        $thisMonth = $thisMonthQuery->sum('amount') ?? 0;

        $hasFilters = !empty($status) || !empty($dateFrom) || !empty($dateTo);

        if (!$hasFilters) {
            $lastMonth = Payment::whereMonth('payment_date', now()->subMonth()->month)
                ->whereYear('payment_date', now()->subMonth()->year)
                ->where('payment_status_id', $completedStatusId)
                ->sum('amount') ?? 0;

            $monthlyGrowth = $lastMonth > 0
                ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
                : 0;
        } else {
            $monthlyGrowth = null;
        }

        $paymentsQuery = Payment::with([
            'booking.user',
            'booking.car',
            'booking.photoReceipt',
            'photoReceipt',
            'paymentStatus',
            'paymentMethod',
            'verifiedByUser:id,name',
            'returnIssue',
        ]);

        if ($status) {
            $statusId = DB::table('payment_statuses')
                ->where('name', ucfirst($status))
                ->value('id');

            $paymentsQuery->where('payment_status_id', $statusId);
        }

        if ($dateFrom) {
            $paymentsQuery->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $paymentsQuery->whereDate('payment_date', '<=', $dateTo);
        }

        $payments = $paymentsQuery
            ->orderBy('payment_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.adminpayment', compact(
            'totalReceived',
            'thisMonth',
            'monthlyGrowth',
            'successfulPayments',
            'payments'
        ));
    }

    public function show($id)
    {
        $payment = Payment::with([
            'booking',
            'paymentStatus',
            'paymentMethod',
            'verifiedByUser:id,name',
            'returnIssue',
            'photoReceipt',
        ])->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    public function approve($paymentId)
    {
        DB::transaction(function () use ($paymentId) {
            $payment = Payment::with([
                'booking',
                'booking.user',
                'photoReceipt',
                'returnIssue',
            ])
                ->lockForUpdate()
                ->findOrFail($paymentId);

            $booking = $payment->booking;
            $receipt = $payment->photoReceipt;

            $completedPaymentStatusId = DB::table('payment_statuses')
                ->where('name', 'Completed')
                ->value('id');

            $payment->update([
                'payment_status_id' => $completedPaymentStatusId,
                'payment_date' => now(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            if ($receipt) {
                $receipt->update([
                    'status' => 'verified',
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }

            if ($payment->return_issue_id && $payment->returnIssue) {
                $resolvedIssueStatus = IssueStatus::where('name', 'resolved')->first();
                $completedBookingStatus = Status::where('name', 'Completed')->first();

                if ($resolvedIssueStatus) {
                    $payment->returnIssue->update([
                        'issue_status_id' => $resolvedIssueStatus->id,
                        'status' => $resolvedIssueStatus->name,
                    ]);
                }

                if ($completedBookingStatus) {
                    $booking->update([
                        'status_id' => $completedBookingStatus->id,
                    ]);
                }

                ReturnIssueHistory::create([
                    'return_issue_id' => $payment->returnIssue->id,
                    'issue_status_id' => $payment->returnIssue->issue_status_id,
                    'changed_by' => auth()->id(),
                    'event_type' => 'payment_verified',
                    'title' => 'Issue Payment Approved',
                    'message' => 'The issue payment was verified by admin. The return issue is now resolved and the booking was marked as completed.',
                    'final_charge' => $payment->returnIssue->final_charge,
                    'booking_status_name' => optional($booking->status)->name,
                ]);

                Notification::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'title' => 'Issue Payment Approved',
                    'message' => 'Your issue payment has been verified. The return issue is now resolved and your booking is marked as completed.',
                    'type' => 'payment',
                    'link' => route('user.return-issues.show', $payment->returnIssue->id),
                ]);
            } else {
                $confirmedStatus = Status::where('name', 'Confirmed')->first();

                if ($confirmedStatus) {
                    $booking->update([
                        'status_id' => $confirmedStatus->id,
                    ]);
                }

                Notification::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'title' => 'Payment Approved',
                    'message' => 'Your payment has been verified and your booking is now confirmed.',
                    'type' => 'payment',
                    'link' => route('user.booking.confirmation', $booking->id),
                ]);
            }
        });

        return back()->with('success', 'Payment approved successfully.');
    }

    public function reject(Request $request, $paymentId)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($request, $paymentId) {
            $payment = Payment::with([
                'booking',
                'booking.user',
                'photoReceipt',
                'returnIssue',
            ])
                ->lockForUpdate()
                ->findOrFail($paymentId);

            $booking = $payment->booking;
            $receipt = $payment->photoReceipt;

            $failedStatusId = DB::table('payment_statuses')
                ->where('name', 'Failed')
                ->value('id');

            $payment->update([
                'payment_status_id' => $failedStatusId,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            if ($receipt) {
                $receipt->update([
                    'status' => 'rejected',
                    'admin_note' => $request->admin_note,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }

            if ($payment->return_issue_id && $payment->returnIssue) {
                ReturnIssueHistory::create([
                    'return_issue_id' => $payment->returnIssue->id,
                    'issue_status_id' => $payment->returnIssue->issue_status_id,
                    'changed_by' => auth()->id(),
                    'event_type' => 'payment_rejected',
                    'title' => 'Issue Payment Rejected',
                    'message' => 'The submitted issue payment receipt was rejected. Reason: ' . $request->admin_note,
                    'final_charge' => $payment->returnIssue->final_charge,
                    'booking_status_name' => optional($booking->status)->name,
                ]);

                Notification::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'title' => 'Issue Payment Rejected',
                    'message' => 'Your issue payment receipt was rejected. Reason: ' . $request->admin_note,
                    'type' => 'payment',
                    'link' => route('user.return-issues.show', $payment->returnIssue->id),
                ]);
            } else {
                $failedStatus = Status::where('name', 'Failed')->first();

                if ($failedStatus) {
                    $booking->update([
                        'status_id' => $failedStatus->id,
                    ]);
                }

                Notification::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'title' => 'Payment Rejected',
                    'message' => 'Your payment receipt was rejected. Reason: ' . $request->admin_note,
                    'type' => 'payment',
                    'link' => route('user.rentals.failed'),
                ]);
            }
        });

        return back()->with('success', 'Payment rejection processed successfully.');
    }
}