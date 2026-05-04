<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentMethods;
use App\Models\PaymentSetting;
use App\Models\PaymentStatus;
use App\Models\PhotoReceipt;
use App\Models\ReturnIssue;
use App\Models\ReturnIssueHistory;
use App\Models\Status;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

public function indexPending()
{
    $awaitingPaymentStatus = \App\Models\PaymentStatus::where('code', 'awaiting_payment')->firstOrFail();
    $pendingPaymentStatus = \App\Models\Status::where('name', 'Pending Payment')->firstOrFail();

    $payments = \App\Models\Payment::with([
            'booking.car.brand',
            'booking.car.transmission',
            'booking.car.fuelType',
            'booking.status',
            'paymentStatus',
        ])
        ->whereNull('return_issue_id')
        ->where('payment_status_id', $awaitingPaymentStatus->id)
        ->whereHas('booking', function ($query) use ($pendingPaymentStatus) {
            $query->where('user_id', auth()->id())
                ->where('status_id', $pendingPaymentStatus->id);
        })
        ->latest()
        ->paginate(10);

    return view('user.indexpayment', compact('payments'));
}



    /**
     * Show payment page
     */
    public function showPayment(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $returnIssueId = $request->query('return_issue_id');

        if (!$bookingId) {
            return redirect()
                ->route('user.browse')
                ->withErrors('Booking ID is required. Please complete your booking.');
        }

        $booking = Booking::with(['car.brand', 'user', 'status'])->find($bookingId);

        if (!$booking) {
            return redirect()
                ->route('user.browse')
                ->withErrors('Booking not found.');
        }

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (($booking->status->name ?? '') === 'Failed') {
            return redirect()
                ->route('user.rentals.failed')
                ->withErrors('This rejected booking cannot be paid again. Please create a new booking.');
        }

        $paymentSetting = PaymentSetting::first();

        $returnIssue = null;
        $paymentType = 'booking';
        $paymentTitle = 'Payment';
        $payableAmount = (float) ($booking->final_total ?? $booking->total_price);

        if ($returnIssueId) {
            $returnIssue = ReturnIssue::with(['issueStatus', 'booking'])->findOrFail($returnIssueId);

            abort_if($returnIssue->booking_id != $booking->id, 404);
            abort_if($returnIssue->booking->user_id !== auth()->id(), 403);

            $issueStatusName = optional($returnIssue->issueStatus)->name ?? $returnIssue->status;

            if ($issueStatusName !== 'awaiting_payment') {
                return redirect()
                    ->route('user.return-issues.show', $returnIssue->id)
                    ->withErrors([
                        'payment' => 'This return issue is not ready for payment yet.',
                    ]);
            }

            if ((float) $returnIssue->final_charge <= 0) {
                return redirect()
                    ->route('user.return-issues.show', $returnIssue->id)
                    ->withErrors([
                        'payment' => 'Final charge is not yet available for this return issue.',
                    ]);
            }

            $paymentType = 'return_issue';
            $paymentTitle = 'Payment';
            $payableAmount = (float) $returnIssue->final_charge;
        }

        return view('user.userpayments', [
            'booking' => $booking,
            'paymentSetting' => $paymentSetting,
            'returnIssue' => $returnIssue,
            'paymentType' => $paymentType,
            'paymentTitle' => $paymentTitle,
            'payableAmount' => $payableAmount,
        ]);
    }

    /**
     * Process manual payment (GCash / Bank)
     */
   /**
 * Process manual payment (GCash / Bank)
 */
public function process(Request $request)
{
    $validated = $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'return_issue_id' => 'nullable|exists:return_issues,id',
        'payment_method' => 'required|in:gcash,bank',
        'receipt_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'bank_receipt' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $booking = Booking::with(['status', 'user'])->findOrFail($validated['booking_id']);

    if ($booking->user_id !== auth()->id()) {
        abort(403);
    }

    $returnIssue = null;
    $amount = (float) ($booking->final_total ?? $booking->total_price);
    $paymentNotes = 'Manual booking payment submitted';

    if (!empty($validated['return_issue_id'])) {
        $returnIssue = ReturnIssue::with(['issueStatus', 'booking'])
            ->findOrFail($validated['return_issue_id']);

        abort_if($returnIssue->booking_id != $booking->id, 404);
        abort_if($returnIssue->booking->user_id !== auth()->id(), 403);

        $issueStatusName = optional($returnIssue->issueStatus)->name ?? $returnIssue->status;

        if ($issueStatusName !== 'awaiting_payment') {
            return back()->withErrors([
                'payment' => 'This return issue is not ready for payment yet.',
            ]);
        }

        if ((float) $returnIssue->final_charge <= 0) {
            return back()->withErrors([
                'payment' => 'Final charge is not yet available for this return issue.',
            ]);
        }

        $amount = (float) $returnIssue->final_charge;
        $paymentNotes = 'Manual return issue payment submitted';
    }

    $receiptPath = null;
    $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

    if ($validated['payment_method'] === 'gcash') {
        if (!$request->hasFile('receipt_image')) {
            return back()->withErrors([
                'receipt_image' => 'Please upload your GCash receipt.',
            ]);
        }

        $receiptPath = $request->file('receipt_image')->store('payment_receipts', $disk);
    }

    if ($validated['payment_method'] === 'bank') {
        if (!$request->hasFile('bank_receipt')) {
            return back()->withErrors([
                'bank_receipt' => 'Please upload your bank transfer receipt.',
            ]);
        }

        $receiptPath = $request->file('bank_receipt')->store('payment_receipts', $disk);
    }

    $paymentMethod = PaymentMethods::where('code', $validated['payment_method'])->firstOrFail();

    $pendingPaymentStatus = PaymentStatus::where('code', 'pending')->firstOrFail();

    $awaitingPaymentStatus = PaymentStatus::firstOrCreate(
        ['code' => 'awaiting_payment'],
        ['name' => 'Awaiting Payment']
    );

    /*
    |--------------------------------------------------------------------------
    | Check if payment already became Pending from a previous failed attempt.
    | If yes, reuse it instead of creating a duplicate or blocking the user.
    |--------------------------------------------------------------------------
    */
    $existingPendingPayment = Payment::where('booking_id', $booking->id)
        ->where('payment_status_id', $pendingPaymentStatus->id)
        ->when(
            $returnIssue,
            function ($query) use ($returnIssue) {
                $query->where('return_issue_id', $returnIssue->id);
            },
            function ($query) {
                $query->whereNull('return_issue_id');
            }
        )
        ->latest()
        ->first();

    if ($existingPendingPayment) {
        $payment = $existingPendingPayment;

        $payment->update([
            'payment_date' => now(),
            'amount' => $amount,
            'payment_method_id' => $paymentMethod->id,
            'payment_status_id' => $pendingPaymentStatus->id,
            'transaction_id' => null,
            'notes' => $paymentNotes,
        ]);
    } elseif ($returnIssue) {
        /*
        |--------------------------------------------------------------------------
        | Return issue payment creates a separate payment row.
        |--------------------------------------------------------------------------
        */
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'return_issue_id' => $returnIssue->id,
            'payment_date' => now(),
            'amount' => $amount,
            'payment_method_id' => $paymentMethod->id,
            'payment_status_id' => $pendingPaymentStatus->id,
            'transaction_id' => null,
            'notes' => $paymentNotes,
        ]);
    } else {
        /*
        |--------------------------------------------------------------------------
        | Normal booking payment updates the existing Awaiting Payment row.
        |--------------------------------------------------------------------------
        */
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
                'notes' => $paymentNotes,
            ]);
        } else {
            /*
            |--------------------------------------------------------------------------
            | Fallback for old bookings created before Awaiting Payment existed.
            |--------------------------------------------------------------------------
            */
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'return_issue_id' => null,
                'payment_date' => now(),
                'amount' => $amount,
                'payment_method_id' => $paymentMethod->id,
                'payment_status_id' => $pendingPaymentStatus->id,
                'transaction_id' => null,
                'notes' => $paymentNotes,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Save payment receipt.
    | Your photo_receipts table requires user_id and supports payment_id,
    | payment_method, status, and return_issue_id.
    |--------------------------------------------------------------------------
    */
    if ($receiptPath) {
        PhotoReceipt::updateOrCreate(
            [
                'booking_id' => $booking->id,
                'return_issue_id' => $returnIssue ? $returnIssue->id : null,
            ],
            [
                'payment_id' => $payment->id,
                'user_id' => auth()->id(),
                'image_path' => $receiptPath,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
            ]
        );
    }

    if (!$returnIssue) {
        $pendingBookingStatus = Status::firstOrCreate([
            'name' => 'Pending Payment Verification',
        ]);

        $booking->update([
            'status_id' => $pendingBookingStatus->id,
        ]);
    }

    if ($returnIssue) {
        ReturnIssueHistory::create([
            'return_issue_id' => $returnIssue->id,
            'user_id' => auth()->id(),
            'action' => 'payment_submitted',
            'description' => 'Customer submitted payment proof for the return issue.',
        ]);

        return redirect()
            ->route('user.return-issues.show', $returnIssue->id)
            ->with('success', 'Payment submitted successfully. Please wait for verification.');
    }

    return redirect()
        ->route('user.booking.confirmation', $booking->id)
        ->with('success', 'Payment submitted successfully. Please wait for verification.');
}

    /**
     * Show payment history
     */
    public function history()
    {
        $payments = Payment::with([
            'booking.car.brand',
            'paymentMethod',
            'paymentStatus',
            'returnIssue',
        ])
            ->whereHas('booking', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('user.payment-history', [
            'payments' => $payments,
        ]);
    }
}