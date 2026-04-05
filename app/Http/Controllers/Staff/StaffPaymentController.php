<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffPaymentController extends Controller
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
            ->withQueryString();

        return view('staff.staffpayment', compact(
            'totalReceived',
            'thisMonth',
            'monthlyGrowth',
            'successfulPayments',
            'payments'
        ));
    }

    public function approve($paymentId)
    {
        DB::transaction(function () use ($paymentId) {
            $payment = Payment::with(['booking', 'booking.photoReceipt', 'booking.user'])
                ->lockForUpdate()
                ->findOrFail($paymentId);

            $booking = $payment->booking;

            $completedStatusId = DB::table('payment_statuses')
                ->where('name', 'Completed')
                ->value('id');

            $payment->update([
                'payment_status_id' => $completedStatusId,
                'payment_date' => now()
            ]);

            $confirmedStatus = Status::where('name', 'Confirmed')->first();

            if ($confirmedStatus) {
                $booking->update([
                    'status_id' => $confirmedStatus->id
                ]);
            }

            if ($booking->photoReceipt) {
                $booking->photoReceipt->update([
                    'status' => 'verified',
                    'verified_by' => auth()->id(),
                    'verified_at' => now()
                ]);
            }

            $pointsEarned = 0;

            if ((int) $booking->points_used === 0) {
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

                if (!$alreadyEarned) {
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

    public function reject(Request $request, $paymentId)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500'
        ]);

        $payment = Payment::with(['booking.photoReceipt'])->findOrFail($paymentId);

        $booking = $payment->booking;
        $receipt = $booking->photoReceipt;

        $failedStatusId = DB::table('payment_statuses')
            ->where('name', 'Failed')
            ->value('id');

        $payment->update([
            'payment_status_id' => $failedStatusId
        ]);

        if ($receipt) {
            $receipt->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);
        }

        $failedStatus = Status::where('name', 'Failed')->first();

        if ($failedStatus) {
            $booking->update([
                'status_id' => $failedStatus->id
            ]);
        }

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
}