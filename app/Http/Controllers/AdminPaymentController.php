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
        // Get total received (all time)
        $completedStatusId = DB::table('payment_statuses')
            ->where('name', 'Completed')
            ->value('id');

        $totalReceived = Payment::where('payment_status_id', $completedStatusId)
            ->sum('amount') ?? 0;

        // Get this month total
        $thisMonth = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->where('payment_status_id', $completedStatusId)
            ->sum('amount') ?? 0;

        // Get last month total for growth calculation
        $lastMonth = Payment::whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->where('payment_status_id', $completedStatusId)
            ->sum('amount') ?? 0;

        // Calculate monthly growth percentage
        $monthlyGrowth = $lastMonth > 0 
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : 0;

        // Get successful payments count
        $successfulPayments = Payment::where('payment_status_id', $completedStatusId)
            ->count();

        // Get recent transactions with filtering
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

        $payments = $paymentsQuery->orderBy('payment_date', 'desc')
            ->paginate(10);

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

        $callback = function() use ($payments) {
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
