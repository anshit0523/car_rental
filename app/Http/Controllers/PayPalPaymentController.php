<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalPaymentController extends Controller
{
    private function getPayPalProvider()
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials([
            'mode'    => env('PAYPAL_MODE', 'sandbox'),
            'sandbox' => [
                'username'      => '',
                'password'      => '',
                'signature'     => '',
                'certificate'   => '',
                'client_id'     => env('PAYPAL_CLIENT_ID'),
                'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            ],
            'live' => [
                'username'      => '',
                'password'      => '',
                'signature'     => '',
                'certificate'   => '',
                'client_id'     => env('PAYPAL_LIVE_CLIENT_ID', ''),
                'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
            ],
            'payment_action' => 'Sale',
            'currency'       => 'PHP',
            'billing_type'   => 'MerchantInitiatedBilling',
            'notify_url'     => '',
            'locale'         => 'en_US',
            'validate_ssl'   => false,  // ⚠️ Temporary for localhost testing
        ]);
        
        $provider->getAccessToken();
        return $provider;
    }

    public function createPayment($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $provider = $this->getPayPalProvider();

        $rentalDays = \Carbon\Carbon::parse($booking->pickup_at)->diffInDays(\Carbon\Carbon::parse($booking->return_at));
        $rentalCost = $booking->car->price_per_day * $rentalDays;
        $insurance = 500;
        $serviceFee = 200;
        $subtotal = $rentalCost + $insurance + $serviceFee;
        $tax = $subtotal * 0.12;
        $total = $subtotal + $tax;

        if ($total <= 0) {
            return redirect()->route('user.payments')->with('error', 'Invalid booking amount!');
        }

        $paypalOrder = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "PHP",
                        "value" => number_format($total, 2, '.', ''),
                    ],
                    "description" => "Car rental booking - " . $booking->car->brand->name . " " . $booking->car->model,
                    "invoice_id" => "booking_" . $booking->id,
                ]
            ],
            "application_context" => [
                "return_url" => route('user.paypal.success', $booking->id),
                "cancel_url" => route('user.paypal.cancel', $booking->id),
            ]
        ]);

        foreach ($paypalOrder['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return redirect()->away($link['href']);
            }
        }

        return redirect()->back()->with('error', 'Something went wrong creating the PayPal order.');
    }

    public function success($booking_id, Request $request)
    {
        $token = $request->get('token');

        if (!$token) {
            return redirect()->route('user.payments')->with('error', 'Invalid PayPal token');
        }

        try {
            $booking = Booking::findOrFail($booking_id);
            
            if ($booking->user_id !== auth()->id()) {
                abort(403, 'Unauthorized');
            }

            $provider = $this->getPayPalProvider();

            \Log::info('About to capture PayPal payment', ['token' => $token]);
            $capture = $provider->capturePaymentOrder($token);
            \Log::info('PayPal capture response', $capture);

            if (isset($capture['status']) && $capture['status'] === 'COMPLETED') {
                $confirmedStatus = Status::firstOrCreate(['name' => 'Confirmed']);
                $booking->update(['status_id' => $confirmedStatus->id]);

                $paymentMethod = PaymentMethod::firstOrCreate(['name' => 'PayPal']);
                $paymentStatus = PaymentStatus::firstOrCreate(['name' => 'Completed']);

                $amount = $this->calculateTotal($booking);

                \Log::info('Payment about to be created', [
                    'booking_id' => $booking_id,
                    'amount' => $amount,
                ]);

                // ✅ Save payment record
                $payment = Payment::create([
                    'booking_id' => $booking_id,
                    'payment_date' => now(),
                    'amount' => $amount,
                    'payment_method_id' => $paymentMethod->id,
                    'payment_status_id' => $paymentStatus->id,
                    'transaction_id' => $capture['id'] ?? null,
                    'notes' => 'PayPal payment for booking #' . $booking_id,
                ]);

                \Log::info('Payment created', ['payment_id' => $payment->id]);

                // ✅ Generate receipt
                Receipt::create([
                    'payment_id' => $payment->id,
                    'booking_id' => $booking_id,
                    'user_id' => auth()->id(),
                    'receipt_number' => 'RCP-' . date('Ymd') . '-' . $payment->id,
                    'amount' => $amount,
                    'status' => 'Generated',
                    'generated_at' => now(),
                ]);

                \Log::info('Receipt created for payment', ['payment_id' => $payment->id]);

                return redirect()->route('user.booking.confirmation', ['id' => $booking_id])
                    ->with('success', 'Payment successful! Your booking is confirmed.');
            }

            // ✅ Payment failed - save failed payment record
            $paymentMethod = PaymentMethod::firstOrCreate(['name' => 'PayPal']);
            $failedStatus = PaymentStatus::firstOrCreate(['name' => 'Failed']);

            Payment::create([
                'booking_id' => $booking_id,
                'payment_date' => now(),
                'amount' => $this->calculateTotal($booking),
                'payment_method_id' => $paymentMethod->id,
                'payment_status_id' => $failedStatus->id,
                'transaction_id' => null,
                'notes' => 'Payment failed. Status: ' . ($capture['status'] ?? 'Unknown'),
            ]);

            return redirect()->route('user.payments')
                ->with('error', 'Payment not completed. Status: ' . ($capture['status'] ?? 'Unknown'));

        } catch (\Exception $e) {
            // Save error payment record
            try {
                $paymentMethod = PaymentMethod::firstOrCreate(['name' => 'PayPal']);
                $errorStatus = PaymentStatus::firstOrCreate(['name' => 'Error']);

                Payment::create([
                    'booking_id' => $booking_id,
                    'payment_date' => now(),
                    'amount' => $this->calculateTotal(Booking::find($booking_id)),
                    'payment_method_id' => $paymentMethod->id,
                    'payment_status_id' => $errorStatus->id,
                    'transaction_id' => null,
                    'notes' => 'Payment error: ' . $e->getMessage(),
                ]);
            } catch (\Exception $ex) {
                \Log::error('Error saving payment record: ' . $ex->getMessage());
            }

            return redirect()->route('user.payments')
                ->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    public function cancel($booking_id, Request $request)
    {
        try {
            $paymentMethod = PaymentMethod::firstOrCreate(['name' => 'PayPal']);
            $cancelledStatus = PaymentStatus::firstOrCreate(['name' => 'Cancelled']);

            Payment::create([
                'booking_id' => $booking_id,
                'payment_date' => now(),
                'amount' => $this->calculateTotal(Booking::find($booking_id)),
                'payment_method_id' => $paymentMethod->id,
                'payment_status_id' => $cancelledStatus->id,
                'transaction_id' => null,
                'notes' => 'Payment cancelled by user',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving cancelled payment: ' . $e->getMessage());
        }

        return redirect()->route('user.payments')
            ->with('error', 'Payment cancelled. Please try again.');
    }

    private function calculateTotal($booking)
    {
        $rentalDays = \Carbon\Carbon::parse($booking->pickup_at)->diffInDays(\Carbon\Carbon::parse($booking->return_at));
        $rentalCost = $booking->car->price_per_day * $rentalDays;
        $insurance = 500;
        $serviceFee = 200;
        $subtotal = $rentalCost + $insurance + $serviceFee;
        $tax = $subtotal * 0.12;
        return $subtotal + $tax;
    }
}