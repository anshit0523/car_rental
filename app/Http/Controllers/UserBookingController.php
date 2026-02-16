<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserBookingController extends Controller
{
    
    /*** Show car details page for booking */
    public function show($carId)
    {
        $car = Car::with(['brand', 'fuelType', 'transmission'])->findOrFail($carId);

        return view('user.usercardetails', [
            'car' => $car,
        ]);
    }

    /**
     * Store a new booking
     */
    /**
 * Store a new booking
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
    ]);

    try {
        // Check if car is available for the selected dates
        $pickupDateTime = Carbon::createFromFormat('Y-m-d H:i', 
            $validated['pickup_date'] . ' ' . $validated['pickup_time']
        );
        
        $returnDateTime = Carbon::createFromFormat('Y-m-d H:i', 
            $validated['return_date'] . ' ' . $validated['return_time']
        );

        // Check for conflicting bookings
        $existingBooking = Booking::where('car_id', $validated['car_id'])
            ->whereIn('status_id', [1, 2, 6]) // reserved, active, confirmed
            ->where(function ($query) use ($pickupDateTime, $returnDateTime) {
                $query->where('pickup_at', '<', $returnDateTime)
                      ->where('return_at', '>', $pickupDateTime);
            })
            ->exists();

        if ($existingBooking) {
            // Return JSON response so JavaScript can show modal
            return response()->json([
                'success' => false,
                'message' => 'This car is not available for the selected dates.',
                'error_type' => 'unavailable_dates'
            ], 422);
        }

        // Get pending status ID
        $pendingStatus = Status::where('name', 'Pending')->first();
        if (!$pendingStatus) {
            $pendingStatus = Status::create(['name' => 'Pending']);
        }

        // Create the booking
        $booking = Booking::create([
            'car_id' => $validated['car_id'],
            'user_id' => auth()->id(),
            'pickup_at' => $pickupDateTime,
            'return_at' => $returnDateTime,
            'total_price' => (float)$validated['total_price'],
            'status_id' => $pendingStatus->id,
        ]);

        \Log::info('Booking created: ' . $booking->id . ' for user: ' . auth()->id());

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully!',
            'redirect' => route('user.payments', ['booking_id' => $booking->id])
        ]);

    } catch (\Exception $e) {
        \Log::error('Booking error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error creating booking: ' . $e->getMessage(),
            'error_type' => 'general_error'
        ], 500);
    }
}

    /**
     * Show payment page
     */
    public function showPayment()
    {
        // Get booking_id from query string
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

        // Check if booking belongs to current user
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('user.userpayments', [
            'booking' => $booking,
        ]);
    }



    /**
 * Process payment
 */
public function processPayment(Request $request)
{
    $validated = $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'payment_method' => 'required|in:card,paypal,apple_pay',
        'card_number' => 'required_if:payment_method,card',
        'expiry_date' => 'required_if:payment_method,card',
        'cvv' => 'required_if:payment_method,card',
        'cardholder_name' => 'required_if:payment_method,card',
    ]);

    try {
        $booking = Booking::findOrFail($validated['booking_id']);

        // ✅ Ensure booking belongs to the logged-in user
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // ✅ Check payment method
        if ($validated['payment_method'] === 'paypal') {
            // Redirect user to PayPal payment route
            return redirect()->route('paypal.payment', ['booking_id' => $booking->id]);
        }

        // ✅ For card or Apple Pay, process immediately
        $confirmedStatus = Status::firstOrCreate(['name' => 'Confirmed']);

        // Update booking status
        $booking->update(['status_id' => $confirmedStatus->id]);

        return redirect()->route('user.booking.confirmation', ['id' => $booking->id])
            ->with('success', 'Payment successful! Your booking is confirmed.');

    } catch (\Exception $e) {
        return back()
            ->withErrors(['error' => 'Payment failed: ' . $e->getMessage()])
            ->withInput();
    }
}


public function confirmation($id)
{
    $booking = Booking::with(['car', 'user', 'payments', 'receipts'])->findOrFail($id);

    // Authorization check
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
    /**
     * Show my bookings list
     */
  public function myActiveBooking()
    {
        return $this->getBookingsByStatus('Active');
    }

    public function upcoming()
    {
        return $this->getBookingsByStatus('Confirmed');
    }

    public function completed()
    {
        return $this->getBookingsByStatus('Completed');
    }

    public function cancelled()
    {
        return $this->getBookingsByStatus('Cancelled');
    }

    private function getBookingsByStatus(string $status)
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->whereHas('status', function ($q) use ($status) {
                $q->where('name', $status);
            })
            ->with(['car.brand', 'car.fuelType', 'car.transmission', 'status'])
            ->orderBy('pickup_at', 'desc')
            ->paginate(10);

        return view('user.userrentals', compact('bookings', 'status'));
    }
 

    /**
     * Cancel a booking
     */
    public function cancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Check authorization
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if booking can be cancelled
        $cancelledStatus = Status::where('name', 'Cancelled')->first();
        
        if ($booking->pickup_at <= Carbon::now()->addHours(24)) {
            return back()->withErrors(['booking' => 'Cannot cancel within 24 hours of pickup.']);
        }

        $booking->update(['status_id' => $cancelledStatus->id]);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Get car details for booking modal/AJAX
     */
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

    /**
     * Check car availability for dates
     */
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after:pickup_date',
        ]);

        $pickupDate = Carbon::parse($validated['pickup_date'])->startOfDay();
        $returnDate = Carbon::parse($validated['return_date'])->endOfDay();

        $isAvailable = !Booking::where('car_id', $validated['car_id'])
            ->where(function ($query) use ($pickupDate, $returnDate) {
                $query->whereBetween('pickup_at', [$pickupDate, $returnDate])
                    ->orWhereBetween('return_at', [$pickupDate, $returnDate])
                    ->orWhere(function ($q) use ($pickupDate, $returnDate) {
                        $q->where('pickup_at', '<=', $pickupDate)
                          ->where('return_at', '>=', $returnDate);
                    });
            })
            ->whereIn('status_id', [1, 2]) // Pending and Confirmed
            ->exists();

        return response()->json(['available' => $isAvailable]);
    }


     /**
     * getUnavailableDates
     */

    /**
 * Get unavailable dates for a car based on existing bookings
 */
public function getUnavailableDates($carId)
    {
        try {
            // Verify the car exists
            $car = Car::findOrFail($carId);
            
            // Get bookings with reserved, active, pending, or confirmed status
            // Status IDs: 1 = reserved, 2 = active, 5 = pending, 6 = confirmed
            $bookings = Booking::where('car_id', $carId)
                ->whereIn('status_id', [1, 2, 5, 6])
                ->get();

            $unavailableDates = [];

            foreach ($bookings as $booking) {
                // Parse the datetime columns to just dates
                $start = Carbon::parse($booking->pickup_at)->startOfDay();
                $end = Carbon::parse($booking->return_at)->startOfDay();

                // Add each date in the range to unavailable dates
                while ($start <= $end) {
                    $unavailableDates[] = $start->format('Y-m-d');
                    $start->addDay();
                }
            }

            // Remove duplicates and return as JSON array
         return response()->json(array_values(array_unique($unavailableDates)));
            
        } catch (\Exception $e) {
            // Return error response for debugging
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}