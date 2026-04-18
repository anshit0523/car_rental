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
            'paymentMethod',
            'verifiedByUser:id,name'
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
            'paymentStatus',
            'paymentMethod',
            'verifiedByUser:id,name'
        ])->findOrFail($id);

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

            $completedStatusId = DB::table('payment_statuses')
                ->where('name', 'Completed')
                ->value('id');

            $payment->update([
                'payment_status_id' => $completedStatusId,
                'payment_date' => now(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
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

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'title' => 'Payment Approved',
                'message' => 'Your payment has been verified and your booking is now confirmed.',
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
            'link' => route('user.rentals.failed')
        ]);

        return back()->with('success', 'Payment rejected and receipt marked as rejected.');
    }


    /**
     * Export payments to CSV
     */
   
}
