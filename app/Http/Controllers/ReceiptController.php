<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    // Display single receipt
    public function show($receipt_id)
    {
        $receipt = Receipt::with(['payment', 'booking', 'user'])->findOrFail($receipt_id);

        // Authorization check
        if ($receipt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'receipt_number' => $receipt->receipt_number,
                'generated_at' => $receipt->generated_at->format('Y-m-d H:i:s'),
                'amount' => $receipt->amount,
                'status' => $receipt->status,
                'payment' => [
                    'id' => $receipt->payment->id,
                    'transaction_id' => $receipt->payment->transaction_id,
                    'payment_method' => $receipt->payment->paymentMethod->name,
                    'payment_status' => $receipt->payment->paymentStatus->name,
                    'payment_date' => $receipt->payment->payment_date->format('Y-m-d H:i:s'),
                ],
                'booking' => [
                    'id' => $receipt->booking->id,
                    'car' => $receipt->booking->car->brand->name . ' ' . $receipt->booking->car->model,
                    'pickup_date' => $receipt->booking->pickup_at,
                    'return_date' => $receipt->booking->return_at,
                    'price_per_day' => $receipt->booking->car->price_per_day,
                ],
                'user' => [
                    'name' => $receipt->user->name,
                    'email' => $receipt->user->email,
                ]
            ]
        ]);
    }

    // Get all receipts for logged-in user
    public function index()
    {
        $receipts = Receipt::where('user_id', auth()->id())
            ->with(['payment', 'booking'])
            ->orderBy('generated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $receipts->count(),
            'data' => $receipts->map(fn($receipt) => [
                'id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'amount' => $receipt->amount,
                'status' => $receipt->status,
                'generated_at' => $receipt->generated_at->format('Y-m-d H:i:s'),
                'booking_id' => $receipt->booking_id,
                'payment_status' => $receipt->payment->paymentStatus->name,
            ])
        ]);
    }

    // Download receipt as PDF
    public function download($receipt_id)
    {
        $receipt = Receipt::with(['payment', 'booking', 'user'])->findOrFail($receipt_id);

        if ($receipt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Update status to Downloaded
        $receipt->update(['status' => 'Downloaded']);

        // Generate PDF content (you can use dompdf or similar)
        $html = $this->generateReceiptHTML($receipt);

        // For now, return HTML view
        return view('receipts.download', ['receipt' => $receipt, 'html' => $html]);
    }

    // Send receipt via email
    public function send($receipt_id)
    {
        $receipt = Receipt::findOrFail($receipt_id);

        if ($receipt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        try {
            // Send email (implement ReceiptMail class)
            \Mail::send(new \App\Mail\ReceiptMail($receipt));

            $receipt->update(['status' => 'Sent']);

            return response()->json([
                'success' => true,
                'message' => 'Receipt sent to ' . $receipt->user->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateReceiptHTML($receipt)
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='text-align: center;'>Payment Receipt</h2>
            <hr>
            
            <p><strong>Receipt Number:</strong> {$receipt->receipt_number}</p>
            <p><strong>Generated Date:</strong> {$receipt->generated_at->format('Y-m-d H:i:s')}</p>
            <p><strong>Status:</strong> {$receipt->status}</p>
            
            <h3>User Information</h3>
            <p><strong>Name:</strong> {$receipt->user->name}</p>
            <p><strong>Email:</strong> {$receipt->user->email}</p>
            
            <h3>Booking Details</h3>
            <p><strong>Car:</strong> {$receipt->booking->car->brand->name} {$receipt->booking->car->model}</p>
            <p><strong>Pickup:</strong> {$receipt->booking->pickup_at}</p>
            <p><strong>Return:</strong> {$receipt->booking->return_at}</p>
            
            <h3>Payment Details</h3>
            <p><strong>Payment Method:</strong> {$receipt->payment->paymentMethod->name}</p>
            <p><strong>Transaction ID:</strong> {$receipt->payment->transaction_id}</p>
            <p><strong>Payment Status:</strong> {$receipt->payment->paymentStatus->name}</p>
            
            <h3>Amount</h3>
            <p style='font-size: 24px; font-weight: bold; color: #2ecc71;'>₱{$receipt->amount}</p>
            
            <hr>
            <p style='text-align: center; color: #999;'>Thank you for your payment!</p>
        </div>
        ";
    }
}