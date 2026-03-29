<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $completedStatusId = DB::table('payment_statuses')
            ->where('name', 'Completed')
            ->value('id');

        // ── Get filter params ─────────────────────────────────────────────────
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // ── Build base query for stats (respects filters) ─────────────────────
        $statsQuery = Payment::query();

        if ($status) {
            $statusId = DB::table('payment_statuses')
                ->where('name', ucfirst($status))
                ->value('id');
            $statsQuery->where('payment_status_id', $statusId);
        } else {
            // If no status filter, only count completed for stats
            $statsQuery->where('payment_status_id', $completedStatusId);
        }

        if ($dateFrom) {
            $statsQuery->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $statsQuery->whereDate('payment_date', '<=', $dateTo);
        }

        // ── Calculate stats based on current filters ──────────────────────────
        $totalReceived = (clone $statsQuery)->sum('amount') ?? 0;
        $successfulPayments = (clone $statsQuery)->count();

        // ── This month calculation (respects date filters if set) ─────────────
        $thisMonthQuery = clone $statsQuery;

        // Only apply "this month" filter if no date range is set
        if (!$dateFrom && !$dateTo) {
            $thisMonthQuery->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year);
        }

        $thisMonth = $thisMonthQuery->sum('amount') ?? 0;

        // ── Monthly growth (only calculate if no filters) ─────────────────────
        $hasFilters = !empty($status) || !empty($dateFrom) || !empty($dateTo);

        if (!$hasFilters) {
            // Calculate monthly growth
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
        // ── Get transactions (uses same filters) ──────────────────────────────
        $paymentsQuery = Payment::with([
            'booking.user',
            'booking.car',
            'booking.photoReceipt',
            'paymentStatus',
            'paymentMethod'
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

        $payments = $paymentsQuery->orderBy('payment_date', 'desc')
            ->paginate(10)
            ->withQueryString(); // Preserve filters in pagination links

        return view('admin.adminpayment', compact(
            'totalReceived',
            'thisMonth',
            'monthlyGrowth',
            'successfulPayments',
            'payments'
        ));
    }

    /**
     * View payment details
     */
    public function show($id)
    {
        $payment = Payment::with([
            'booking',
            'paymentStatus'
        ])
            ->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }


    // aprove payment and update booking status to confirmed
    public function approve($paymentId)
{
    DB::transaction(function () use ($paymentId) {
        $payment = Payment::with(['booking', 'booking.photoReceipt', 'booking.user'])
            ->lockForUpdate()
            ->findOrFail($paymentId);

        $booking = $payment->booking;
        $user = $booking->user;

        // Get completed payment status
        $completedStatusId = DB::table('payment_statuses')
            ->where('name', 'Completed')
            ->value('id');

        // Update payment
        $payment->update([
            'payment_status_id' => $completedStatusId,
            'payment_date' => now()
        ]);

        // Update booking status to Confirmed
        $confirmedStatus = Status::where('name', 'Confirmed')->first();

        if ($confirmedStatus) {
            $booking->update([
                'status_id' => $confirmedStatus->id
            ]);
        }

        // Update receipt verification
        if ($booking->photoReceipt) {
            $booking->photoReceipt->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GIVE POINTS ONLY IF NO REDEEM WAS USED
        |--------------------------------------------------------------------------
        */
        if ((int) $booking->points_used === 0) {

            // Prevent duplicate earn if admin clicks approve twice
            $alreadyEarned = DB::table('points_transactions')
                ->where('user_id', $booking->user_id)
                ->where('booking_id', $booking->id)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('points_transaction_types')
                        ->whereColumn('points_transaction_types.id', 'points_transactions.points_id')
                        ->where('points_transaction_types.name', 'earn');
                })
                ->exists();

            if (! $alreadyEarned) {
                //rule: earn 1 point for every $100 spent----
              $pointsEarned = (int) floor($booking->final_total / 100);

                if ($pointsEarned > 0) {
                    $earnTypeId = (int) DB::table('points_transaction_types')
                        ->where('name', 'earn')
                        ->value('id');

                    $userRow = DB::table('users')
                        ->where('id', $booking->user_id)
                        ->lockForUpdate()
                        ->first();

                    $balanceBefore = (int) $userRow->points_balance;
                    $balanceAfter = $balanceBefore + $pointsEarned;

                    DB::table('users')
                        ->where('id', $booking->user_id)
                        ->update([
                            'points_balance' => $balanceAfter,
                            'updated_at' => now(),
                        ]);

                    DB::table('points_transactions')->insert([
                        'user_id' => $booking->user_id,
                        'booking_id' => $booking->id,
                        'points_id' => $earnTypeId,
                        'points_change' => $pointsEarned,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'note' => 'Points earned from approved booking',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $notificationMessage = 'Your payment has been verified and your booking is now confirmed.';

        if ((int) $booking->points_used > 0) {
            $notificationMessage .= ' No points were earned because redeemed points were used for this booking.';
        } else {
            $notificationMessage .= " You earned {$pointsEarned} points from this booking.";
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'title' => 'Payment Approved',
            'message' => $notificationMessage,
            'type' => 'payment',
            'link' => route('user.booking.confirmation', $booking->id)
        ]);
    });

    return back()->with('success', 'Payment approved and booking confirmed.');
}

    // reject payment and update receipt status to rejected
    public function reject(Request $request, $paymentId)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500'
        ]);

        $payment = Payment::with(['booking.photoReceipt'])->findOrFail($paymentId);

        $booking = $payment->booking;
        $receipt = $booking->photoReceipt;

        // Get payment status ID
        $failedStatusId = DB::table('payment_statuses')
            ->where('name', 'Failed')
            ->value('id');

        // Update payment
        $payment->update([
            'payment_status_id' => $failedStatusId
        ]);

        // Update receipt
        if ($receipt) {
            $receipt->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
        }

        // Update booking status to Failed
        $failedStatus = Status::where('name', 'Failed')->first();

        if ($failedStatus) {
            $booking->update([
                'status_id' => $failedStatus->id
            ]);
        }

        // Send notification
        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'title' => 'Payment Rejected',
            'message' => 'Your payment receipt was rejected. Reason: ' . $request->admin_note,
            'type' => 'payment',
            'link' => route('user.rentals.pending', $booking->id)
        ]);

        return back()->with('error', 'Payment rejected.');
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $paymentsQuery = Payment::with([
            'booking.user',
            'booking.car',
            'paymentStatus'
        ]);

        if ($status) {
            $statusId = DB::table('payment_statuses')
                ->where('name', ucfirst($status))
                ->value('id');
            $paymentsQuery = $paymentsQuery->where('payment_status_id', $statusId);
        }

        if ($dateFrom) {
            $paymentsQuery = $paymentsQuery->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $paymentsQuery = $paymentsQuery->whereDate('payment_date', '<=', $dateTo);
        }

        $payments = $paymentsQuery->orderBy('payment_date', 'desc')->get();

        $csvFileName = 'payments_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$csvFileName\""
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['Date', 'Booking ID', 'Customer', 'Amount', 'Method', 'Status', 'Transaction ID']);

            // Data rows
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_date->format('Y-m-d H:i:s'),
                    'BK-' . $payment->booking_id,
                    $payment->booking->user->name ?? 'N/A',
                    $payment->amount,
                    'PayPal', // Since we're only using PayPal for now
                    $payment->paymentStatus->name ?? 'Unknown',
                    $payment->transaction_id ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
