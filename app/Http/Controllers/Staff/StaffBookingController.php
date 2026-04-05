<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Status;
use Illuminate\Http\Request;

class StaffBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'car.brand', 'status']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->orWhereHas('car', function ($c) use ($search) {
                    $c->where('model', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $bookings = $query->latest()->paginate(10);
        $statuses = Status::all();

        return view('staff.staffbooking', compact('bookings', 'statuses'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
            'admin_message' => 'nullable|string|max:500',
        ]);

        $newStatus = Status::findOrFail($request->status_id);
        $currentStatus = $booking->status->name ?? null;

        $allowedTransitions = [
            'Pending' => ['Cancelled'],
            'Confirmed' => ['Cancelled'],
            'Return' => ['Checkup', 'Damage', 'Needs Repair', 'Completed'],
            'Active' => [],
            'Completed' => [],
            'Cancelled' => [],
            'Checkup' => [],
            'Damage' => [],
            'Needs Repair' => [],
            'Failed' => [],
        ];

        if (!array_key_exists($currentStatus, $allowedTransitions)) {
            return redirect()
                ->route('staff.bookings.index')
                ->with('error', 'Invalid current booking status.');
        }

        if (!in_array($newStatus->name, $allowedTransitions[$currentStatus])) {
            return redirect()
                ->route('staff.bookings.index')
                ->with('error', "Cannot change status from {$currentStatus} to {$newStatus->name}.");
        }

        $booking->update([
            'status_id' => $newStatus->id,
        ]);

        $adminMessage = trim($request->admin_message ?? '');

        if ($currentStatus === 'Return') {
            $notificationMessage = "Your booking status has been updated to {$newStatus->name}.";

            if (!empty($adminMessage)) {
                $notificationMessage .= " Admin note: {$adminMessage}";
            }

            $title = match ($newStatus->name) {
                'Damage' => 'Vehicle Damage Notice',
                'Needs Repair' => 'Vehicle Repair Notice',
                'Checkup' => 'Vehicle Checkup Notice',
                'Completed' => 'Booking Completed',
                default => 'Booking Return Update',
            };

            Notification::create([
                'user_id' => $booking->user_id,
                'booking_id' => $booking->id,
                'title' => $title,
                'message' => $notificationMessage,
                'type' => 'booking',
                'link' => route('user.booking.confirmation', $booking->id),
            ]);
        }

        return redirect()
            ->route('staff.bookings.index')
            ->with('success', 'Booking status updated successfully.');
    }
}