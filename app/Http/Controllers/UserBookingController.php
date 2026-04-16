<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use App\Models\PaymentSetting;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserBookingController extends Controller
{
    /**
     * Show car details page for booking
     */
    public function show($carId)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission'])->findOrFail($carId);
        $serviceTypes = DB::table('service_types')->orderBy('id')->get();

        return view('user.usercardetails', [
            'car' => $car,
            'serviceTypes' => $serviceTypes,
        ]);
    }

    /**
     * Guest can fill the booking form.
     * If not logged in yet, keep the booking data in session,
     * then redirect to login/register.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|date_format:H:i',
            'return_date' => 'required|date|after:pickup_date',
            'return_time' => 'required|date_format:H:i',
            'total_price' => 'required|numeric|min:0',
            'service_type_id' => 'required|exists:service_types,id',
            'service_location' => 'nullable|string|max:255',
            'phone' => 'required|string|max:30',
        ]);

        $serviceTypeName = DB::table('service_types')
            ->where('id', $validated['service_type_id'])
            ->value('name');

        if (stripos($serviceTypeName, 'deliver') !== false && empty($validated['service_location'])) {
            return back()
                ->withErrors(['service_location' => 'Location is required for Delivery.'])
                ->withInput();
        }

        // Guest flow: save entered booking data first, then ask user to login/register.
        if (!auth()->check()) {
            session([
                'guest_booking_payload' => $validated,
            ]);

            return redirect()->route('login')
                ->with('info', 'Please sign in or create an account to continue to payment.');
        }

        try {
            $booking = $this->createBookingForUser(auth()->user(), $validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully!',
                    'redirect' => route('user.payments', ['booking_id' => $booking->id]),
                ]);
            }

            return redirect()
                ->route('user.payments', ['booking_id' => $booking->id])
                ->with('success', 'Booking created successfully!');
        } catch (\RuntimeException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error_type' => 'unavailable_dates',
                ], 422);
            }

            return back()
                ->withErrors(['booking' => $e->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Booking error: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating booking.',
                    'error_type' => 'general_error',
                ], 500);
            }

            return back()
                ->withErrors(['booking' => 'Error creating booking. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Called right after login/register.
     * Creates the real booking from the guest session,
     * then redirects directly to payment page.
     */
    public function continueGuestBooking()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $payload = session('guest_booking_payload');

        if (!$payload) {
            return redirect()->route('user.browse')
                ->with('error', 'No pending booking found. Please select a car again.');
        }

        try {
            $booking = $this->createBookingForUser(auth()->user(), $payload);

            session()->forget('guest_booking_payload');

            return redirect()
                ->route('user.payments', ['booking_id' => $booking->id])
                ->with('success', 'Booking created successfully. Please continue with payment.');
        } catch (\RuntimeException $e) {
            session()->forget('guest_booking_payload');

            return redirect()
                ->route('user.browse')
                ->withErrors(['booking' => $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('Continue guest booking error: ' . $e->getMessage());

            session()->forget('guest_booking_payload');

            return redirect()
                ->route('user.browse')
                ->withErrors(['booking' => 'Error creating booking. Please try again.']);
        }
    }

    /**
     * Shared booking creation logic for logged-in users and guest-then-login flow.
     */
    private function createBookingForUser($user, array $validated): Booking
    {
        if (!$user->phone || $user->phone !== $validated['phone']) {
            $user->update(['phone' => $validated['phone']]);
        }

        $pickupDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['pickup_date'] . ' ' . $validated['pickup_time']
        );

        $returnDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['return_date'] . ' ' . $validated['return_time']
        );

        $pendingStatus = Status::firstOrCreate(['name' => 'Pending']);

        return DB::transaction(function () use (
            $user,
            $validated,
            $pickupDateTime,
            $returnDateTime,
            $pendingStatus
        ) {
            $existingBooking = Booking::where('car_id', $validated['car_id'])
                ->whereIn('status_id', [1, 2, 6])
                ->where(function ($query) use ($pickupDateTime, $returnDateTime) {
                    $query->where('pickup_at', '<', $returnDateTime)
                        ->where('return_at', '>', $pickupDateTime);
                })
                ->lockForUpdate()
                ->exists();

            if ($existingBooking) {
                throw new \RuntimeException('This car is not available for the selected dates.');
            }

            $totalPrice = (float) $validated['total_price'];

            return Booking::create([
                'car_id' => $validated['car_id'],
                'user_id' => $user->id,
                'pickup_at' => $pickupDateTime,
                'return_at' => $returnDateTime,
                'total_price' => $totalPrice,
                'final_total' => $totalPrice,
                'status_id' => $pendingStatus->id,
                'service_type_id' => $validated['service_type_id'],
                'service_location' => $validated['service_location'] ?? null,
            ]);
        });
    }

    public function confirmation($id)
    {
        $booking = Booking::with(['car', 'user', 'payments', 'receipts'])->findOrFail($id);

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $payment = $booking->payments()->latest()->first();
        $receipt = $booking->receipts()->latest()->first();

        return view('user.booking-confirmation', [
            'booking' => $booking,
            'payment' => $payment,
            'receipt' => $receipt,
        ]);
    }

    public function showPayment()
    {
        $bookingId = request()->query('booking_id');

        if (!$bookingId) {
            return redirect()->route('user.browse')
                ->withErrors('Booking ID is required. Please complete your booking.');
        }

        $booking = Booking::with(['car', 'user', 'status'])->find($bookingId);

        if (!$booking) {
            return redirect()->route('user.browse')
                ->withErrors('Booking not found.');
        }

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $paymentSetting = PaymentSetting::first();

        return view('user.userpayments', [
            'booking' => $booking,
            'paymentSetting' => $paymentSetting,
        ]);
    }

    public function cancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($booking->pickup_at <= Carbon::now()->addHours(24)) {
            return back()->withErrors(['booking' => 'Cannot cancel within 24 hours of pickup.']);
        }

        $cancelledStatus = Status::firstOrCreate(['name' => 'Cancelled']);
        $booking->update(['status_id' => $cancelledStatus->id]);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    public function getCarDetails($carId)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission'])->findOrFail($carId);

        return response()->json([
            'id' => $car->id,
            'name' => $car->brand->name . ' ' . $car->model,
            'price_per_day' => $car->price_per_day,
            'seats' => $car->seats,
            'fuel_type' => $car->fuelType->type,
            'transmission' => $car->transmission->type,
        ]);
    }
}