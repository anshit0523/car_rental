<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
