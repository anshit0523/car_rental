<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $statusIds = Status::whereIn('name', [
            'Pending',
            'Active',
            'Return',
            'Checkup',
            'Damage',
            'Needs Repair',
        ])->pluck('id', 'name');

        $paymentStatusIds = DB::table('payment_statuses')
            ->whereIn('name', ['Pending', 'Completed'])
            ->pluck('id', 'name');

        $pendingStatusId = $statusIds['Pending'] ?? null;
        $activeStatusId = $statusIds['Active'] ?? null;
        $returnStatusId = $statusIds['Return'] ?? null;

        $attentionStatusIds = array_values(array_filter([
            $statusIds['Checkup'] ?? null,
            $statusIds['Damage'] ?? null,
            $statusIds['Needs Repair'] ?? null,
        ]));

        $pendingPaymentStatusId = $paymentStatusIds['Pending'] ?? null;

        $pendingBookings = $pendingStatusId
            ? Booking::where('status_id', $pendingStatusId)->count()
            : 0;

        $pendingPayments = $pendingPaymentStatusId
            ? Payment::where('payment_status_id', $pendingPaymentStatusId)->count()
            : 0;

        $activeRentals = $activeStatusId
            ? Booking::where('status_id', $activeStatusId)->count()
            : 0;

        $returnsToday = $returnStatusId
            ? Booking::where('status_id', $returnStatusId)
                ->whereDate('return_at', $today)
                ->count()
            : 0;

        $attentionCars = !empty($attentionStatusIds)
            ? Booking::whereIn('status_id', $attentionStatusIds)->count()
            : 0;

        $recentBookings = Booking::with(['user', 'car.brand', 'status'])
            ->latest()
            ->limit(5)
            ->get();

        $todayPickupsBase = Booking::with(['user', 'car.brand', 'status'])
            ->whereDate('pickup_at', $today)
            ->orderBy('pickup_at');

        $todayReturnsBase = Booking::with(['user', 'car.brand', 'status'])
            ->whereDate('return_at', $today)
            ->orderBy('return_at');

        $todayPickupsCount = (clone $todayPickupsBase)->count();
        $todayReturnsCount = (clone $todayReturnsBase)->count();

        $todayPickups = $todayPickupsBase->limit(6)->get();
        $todayReturns = $todayReturnsBase->limit(6)->get();

        $attentionBookings = !empty($attentionStatusIds)
            ? Booking::with(['user', 'car.brand', 'status'])
                ->whereIn('status_id', $attentionStatusIds)
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        return view('staff.staffdashboard', [
            'pendingBookings' => $pendingBookings,
            'pendingPayments' => $pendingPayments,
            'activeRentals' => $activeRentals,
            'returnsToday' => $returnsToday,
            'attentionCars' => $attentionCars,
            'recentBookings' => $recentBookings,
            'todayPickups' => $todayPickups,
            'todayReturns' => $todayReturns,
            'todayPickupsCount' => $todayPickupsCount,
            'todayReturnsCount' => $todayReturnsCount,
            'attentionBookings' => $attentionBookings,
        ]);
    }
}