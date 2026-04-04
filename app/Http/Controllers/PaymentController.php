<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentMethods;
use App\Models\PaymentStatus;
use App\Models\PhotoReceipt;
use App\Models\Receipt;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{

    /**
     * Show payment page
     */
    public function show($bookingId)
    {
        $booking = Booking::with(['car','user'])->findOrFail($bookingId);

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.userpayments', [
            'booking' => $booking
        ]);
    }

    /**
     * Process manual payment (GCash / Bank)
     */
 public function process(Request $request)
{
    $validated = $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'payment_method' => 'required|in:gcash,bank',
        'receipt_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'bank_receipt' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $booking = Booking::findOrFail($validated['booking_id']);

    if ($booking->user_id !== auth()->id()) {
        abort(403);
    }

    $receiptPath = null;

    if ($validated['payment_method'] === 'gcash') {
        if (!$request->hasFile('receipt_image')) {
            return back()->withErrors([
                'receipt_image' => 'Please upload your GCash receipt.'
            ]);
        }

        $receiptPath = $request->file('receipt_image')
            ->store('payment_receipts', 'public');
    }

    if ($validated['payment_method'] === 'bank') {
        if (!$request->hasFile('bank_receipt')) {
            return back()->withErrors([
                'bank_receipt' => 'Please upload your bank transfer receipt.'
            ]);
        }

        $receiptPath = $request->file('bank_receipt')
            ->store('payment_receipts', 'public');
    }

    $paymentMethod = PaymentMethods::where('code', $validated['payment_method'])->firstOrFail();
    $paymentStatus = PaymentStatus::where('code', 'pending')->firstOrFail();

    // check if pending payment already exists for this booking
    $existingPayment = Payment::where('booking_id', $booking->id)
        ->where('payment_status_id', $paymentStatus->id)
        ->first();

    if ($existingPayment) {
        return back()->withErrors([
            'payment' => 'A pending payment already exists for this booking.'
        ]);
    }

  $payment = Payment::firstOrCreate(
    [
        'booking_id' => $booking->id,
        'payment_status_id' => $paymentStatus->id,
    ],
    [
        'payment_date' => now(),
        'amount' => $booking->final_total ?? $booking->total_price,
        'payment_method_id' => $paymentMethod->id,
        'notes' => 'Manual payment submitted',
    ]
);

    PhotoReceipt::create([
        'booking_id' => $booking->id,
        'payment_id' => $payment->id,
        'user_id' => auth()->id(),
        'image_path' => $receiptPath,
        'payment_method' => $validated['payment_method'],
        'status' => 'pending'
    ]);

    $pendingStatus = Status::firstOrCreate([
        'name' => 'Pending Payment Verification'
    ]);

    $booking->update([
        'status_id' => $pendingStatus->id
    ]);

    return redirect()
        ->route('user.rentals.pending')
        ->with('success', 'Payment proof submitted. Waiting for verification.');
}

    /**
     * Show payment history
     */
    public function history()
    {

        $payments = Payment::whereHas('booking', function($q){
            $q->where('user_id', auth()->id());
        })->latest()->get();


        return view('user.payment-history', [
            'payments' => $payments
        ]);
    }

}
