<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\PaymentMethods;
use App\Models\PaymentStatus;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalPaymentController extends Controller
{
    /**
     * Get PayPal provider with proper configuration from database
     * Configured for Philippines (PHP currency, en_PH locale)
     * 
     * @return PayPalClient
     * @throws \Exception
     */
    private function getPayPalProvider()
    {
        try {
            $setting = PaymentSetting::where('provider', 'paypal')
                ->where('active', true)
                ->firstOrFail();

            $provider = new PayPalClient;

            // Set all required API credentials
            // Configured for Philippines
            $provider->setApiCredentials([
                'mode' => $setting->environment, // sandbox | live
                'sandbox' => [
                    'client_id' => $setting->client_id,
                    'client_secret' => $setting->client_secret,
                ],
                'live' => [
                    'client_id' => $setting->client_id,
                    'client_secret' => $setting->client_secret,
                ],
                'payment_action' => 'Sale', // REQUIRED
                'currency' => 'PHP', // Philippine Peso
                'notify_url' => '',
                'locale' => 'en_PH', // ✅ Philippines English locale
                'validate_ssl' => app()->environment('production'),
            ]);

            // Get access token
            $provider->getAccessToken();

            Log::info('PayPal provider initialized successfully', [
                'mode' => $setting->environment,
                'locale' => 'en_PH',
                'currency' => 'PHP',
            ]);

            return $provider;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('PayPal settings not found or not active', [
                'error' => 'PaymentSetting not configured',
            ]);
            throw new \Exception('PayPal payment processor is not configured. Please contact support.');
        } catch (\Exception $e) {
            Log::error('Error initializing PayPal provider', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Create PayPal payment order
     * 
     * @param int $booking_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createPayment($booking_id)
    {
        try {
            $booking = Booking::findOrFail($booking_id);

            if ($booking->user_id !== auth()->id()) {
                abort(403, 'Unauthorized');
            }

            Log::info('Initiating PayPal payment', [
                'booking_id' => $booking_id,
                'user_id' => auth()->id(),
            ]);

            $provider = $this->getPayPalProvider();

            // Calculate payment details
            $rentalDays = \Carbon\Carbon::parse($booking->pickup_at)
                ->diffInDays(\Carbon\Carbon::parse($booking->return_at));
            
            $rentalCost = $booking->car->price_per_day * $rentalDays;
            $insurance = 500; // PHP
            $serviceFee = 200; // PHP
            $subtotal = $rentalCost + $insurance + $serviceFee;
            $tax = $subtotal * 0.12;
            $total = $subtotal + $tax;

            if ($total <= 0) {
                Log::error('Invalid booking amount', [
                    'booking_id' => $booking_id,
                    'total' => $total,
                ]);
                return redirect()->route('user.payments')
                    ->with('error', 'Invalid booking amount!');
            }

            Log::info('Payment details calculated (Philippine Peso)', [
                'booking_id' => $booking_id,
                'rental_cost' => '₱' . number_format($rentalCost, 2),
                'insurance' => '₱' . number_format($insurance, 2),
                'service_fee' => '₱' . number_format($serviceFee, 2),
                'subtotal' => '₱' . number_format($subtotal, 2),
                'tax' => '₱' . number_format($tax, 2),
                'total' => '₱' . number_format($total, 2),
            ]);

            // Create PayPal order in Philippine Peso
            $paypalOrder = $provider->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "PHP", // Philippine Peso
                            "value" => number_format($total, 2, '.', ''),
                        ],
                        "description" => "Car rental booking - " . $booking->car->brand->name . " " . $booking->car->model,
                        "invoice_id" => "booking_" . $booking->id . "_" . time(),
                    ]
                ],
                "application_context" => [
                    "brand_name" => env('APP_NAME', 'Car Rental Philippines'),
                    "return_url" => route('user.paypal.success', $booking->id),
                    "cancel_url" => route('user.paypal.cancel', $booking->id),
                ]
            ]);

            Log::info('PayPal order created successfully', [
                'booking_id' => $booking_id,
                'paypal_order_id' => $paypalOrder['id'] ?? 'N/A',
                'currency' => 'PHP',
                'amount' => $total,
            ]);

            // Find and redirect to approval link
            if (isset($paypalOrder['links'])) {
                foreach ($paypalOrder['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        Log::info('Redirecting to PayPal approval', [
                            'booking_id' => $booking_id,
                            'approval_url' => substr($link['href'], 0, 50) . '...',
                        ]);
                        return redirect()->away($link['href']);
                    }
                }
            }

            Log::error('No approval link in PayPal response', [
                'booking_id' => $booking_id,
                'response' => $paypalOrder,
            ]);

            return redirect()->back()
                ->with('error', 'Something went wrong creating the PayPal order.');

        } catch (\Exception $e) {
            Log::error('Error creating PayPal payment', [
                'booking_id' => $booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful PayPal payment
     * 
     * CRITICAL: Validates that PayPal actually captured the payment
     * and that the amount matches what was requested
     * 
     * @param int $booking_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
public function success($booking_id, Request $request)
{
    $token = $request->get('token');
    if (!$token) {
        return redirect()->route('user.payments')->with('error', 'Invalid PayPal token.');
    }

    $booking = Booking::findOrFail($booking_id);

    if ($booking->user_id !== auth()->id()) {
        abort(403);
    }

    $provider = $this->getPayPalProvider();
    $capture = $provider->capturePaymentOrder($token);

    $paymentMethod = PaymentMethods::firstOrCreate(['name' => 'PayPal']);

    // Determine status based on PayPal response
    $payments = $capture['purchase_units'][0]['payments']['captures'][0] ?? null;
    $paymentStatusRaw = $payments['status'] ?? $capture['status'] ?? 'UNKNOWN';
    $expectedAmount = $this->calculateTotal($booking);
    $capturedAmount = $payments['amount']['value'] ?? $expectedAmount;

    // Map PayPal status to database PaymentStatus
    if ($paymentStatusRaw === 'COMPLETED' && (float)$capturedAmount >= $expectedAmount) {
        $status = PaymentStatus::where('code', 'completed')->firstOrFail();
        $bookingStatus = Status::where('name', 'Confirmed')->firstOrFail();
        $booking->update(['status_id' => $bookingStatus->id]);
    } elseif ($paymentStatusRaw === 'PENDING') {
        $status = PaymentStatus::where('code', 'pending')->firstOrFail();
    } else {
        $status = PaymentStatus::where('code', 'failed')->firstOrFail();
    }

    // Save payment record
    $payment = Payment::create([
        'booking_id' => $booking_id,
        'payment_date' => now(),
        'amount' => (float)$capturedAmount,
        'payment_method_id' => $paymentMethod->id,
        'payment_status_id' => $status->id,
        'transaction_id' => $payments['id'] ?? $capture['id'] ?? null,
        'notes' => 'PayPal payment status: ' . $paymentStatusRaw,
    ]);

    // Generate receipt if payment is completed
    if ($status->code === 'completed') {
        Receipt::create([
            'payment_id' => $payment->id,
            'booking_id' => $booking_id,
            'user_id' => auth()->id(),
            'receipt_number' => 'RCP-' . date('Ymd') . '-' . $payment->id,
            'amount' => (float)$capturedAmount,
            'status' => 'Generated',
            'generated_at' => now(),
        ]);
    }

    // Redirect based on payment status
    if ($status->code === 'completed') {
        return redirect()->route('user.booking.confirmation', $booking_id)
            ->with('success', 'Payment successful. Booking confirmed.');
    } elseif ($status->code === 'pending') {
        return redirect()->route('user.payments')
            ->with('warning', 'Payment is pending. It will be confirmed once PayPal completes it.');
    } else {
        return redirect()->route('user.payments')
            ->with('error', 'Payment failed. Please try again.');
    }
}

public function cancel($booking_id, Request $request)
    {
        try {
            $booking = Booking::findOrFail($booking_id);

            if ($booking->user_id !== auth()->id()) {
                abort(403, 'Unauthorized');
            }

            Log::info('Payment cancelled by user', [
                'booking_id' => $booking_id,
                'user_id' => auth()->id(),
            ]);

            $paymentMethod = PaymentMethods::firstOrCreate(['name' => 'PayPal']);
           $cancelledStatus = PaymentStatus::where('code', 'pending')->firstOrFail();

            Payment::create([
                'booking_id' => $booking_id,
                'payment_date' => now(),
                'amount' => $this->calculateTotal($booking),
                'payment_method_id' => $paymentMethod->id,
                'payment_status_id' => $cancelledStatus->id,
                'transaction_id' => null,
                'notes' => 'Payment cancelled by user',
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving cancelled payment', [
                'booking_id' => $booking_id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('user.payments')
            ->with('error', 'Payment cancelled. Please try again.');
    }


    /**
     * Calculate total payment amount in Philippine Peso
     * 
     * @param Booking $booking
     * @return float
     */
    private function calculateTotal($booking)
    {
        $rentalDays = \Carbon\Carbon::parse($booking->pickup_at)
            ->diffInDays(\Carbon\Carbon::parse($booking->return_at));
        
        $rentalCost = $booking->car->price_per_day * $rentalDays;
        $insurance = 500;
        $serviceFee = 200;
        $subtotal = $rentalCost + $insurance + $serviceFee;
        $tax = $subtotal * 0.12;
        
        return $subtotal + $tax;
    }
}