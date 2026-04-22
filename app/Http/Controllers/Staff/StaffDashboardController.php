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
            'Reserved',
            'Confirmed',
            'Active',
            'Return',
            'Completed',
            'Cancelled',
            'Failed',
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

        $returnsToday = Booking::whereDate('return_at', $today)->count();

        $attentionCars = !empty($attentionStatusIds)
            ? Booking::whereIn('status_id', $attentionStatusIds)->count()
            : 0;

        $recentBookings = Booking::with(['user', 'car.brand', 'status'])
            ->latest()
            ->limit(5)
            ->get();

        $pickupStatusIds = array_values(array_filter([
            $statusIds['Pending'] ?? null,
            $statusIds['Reserved'] ?? null,
            $statusIds['Confirmed'] ?? null,
            $statusIds['Active'] ?? null,
        ]));

        $returnStatusTableIds = array_values(array_filter([
            $statusIds['Active'] ?? null,
            $statusIds['Return'] ?? null,
            $statusIds['Completed'] ?? null,
        ]));

        $processedPickupStatusIds = array_values(array_filter([
            $statusIds['Active'] ?? null,
            $statusIds['Completed'] ?? null,
            $statusIds['Cancelled'] ?? null,
            $statusIds['Failed'] ?? null,
        ]));

        $completedStatusId = $statusIds['Completed'] ?? 0;

        $todayPickupsBase = Booking::with(['user', 'car.brand', 'status'])
            ->whereDate('pickup_at', $today)
            ->when(!empty($pickupStatusIds), function ($query) use ($pickupStatusIds) {
                $query->whereIn('status_id', $pickupStatusIds);
            })
            ->orderByRaw(
                'CASE WHEN pickup_at < NOW() AND status_id NOT IN (' . implode(',', !empty($processedPickupStatusIds) ? $processedPickupStatusIds : [0]) . ') THEN 0 ELSE 1 END'
            )
            ->orderBy('pickup_at');

        $todayReturnsBase = Booking::with(['user', 'car.brand', 'status'])
            ->whereDate('return_at', $today)
            ->when(!empty($returnStatusTableIds), function ($query) use ($returnStatusTableIds) {
                $query->whereIn('status_id', $returnStatusTableIds);
            })
            ->orderByRaw(
                'CASE WHEN return_at < NOW() AND status_id != ? THEN 0 ELSE 1 END',
                [$completedStatusId]
            )
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