<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffDashboardController extends Controller
{
     public function index()
    {
        $today = now()->toDateString();

        $pendingStatusId = Status::where('name', 'Pending')->value('id');
        $confirmedStatusId = Status::where('name', 'Confirmed')->value('id');
        $activeStatusId = Status::where('name', 'Active')->value('id');
        $returnStatusId = Status::where('name', 'Return')->value('id');
        $checkupStatusId = Status::where('name', 'Checkup')->value('id');
        $damageStatusId = Status::where('name', 'Damage')->value('id');
        $repairStatusId = Status::where('name', 'Needs Repair')->value('id');

        $completedPaymentStatusId = DB::table('payment_statuses')
            ->where('name', 'Completed')
            ->value('id');

        $pendingPaymentStatusId = DB::table('payment_statuses')
            ->where('name', 'Pending')
            ->value('id');

        $pendingBookings = Booking::when($pendingStatusId, function ($query) use ($pendingStatusId) {
                $query->where('status_id', $pendingStatusId);
            })
            ->count();

        $pendingPayments = Payment::when($pendingPaymentStatusId, function ($query) use ($pendingPaymentStatusId) {
                $query->where('payment_status_id', $pendingPaymentStatusId);
            })
            ->count();

        $activeRentals = Booking::when($activeStatusId, function ($query) use ($activeStatusId) {
                $query->where('status_id', $activeStatusId);
            })
            ->count();

        $returnsToday = Booking::when($returnStatusId, function ($query) use ($returnStatusId) {
                $query->where('status_id', $returnStatusId);
            })
            ->whereDate('return_at', $today)
            ->count();

        $attentionCars = Booking::whereIn('status_id', array_filter([
                $checkupStatusId,
                $damageStatusId,
                $repairStatusId,
            ]))
            ->count();

        $recentBookings = Booking::with(['user', 'car.brand', 'status'])
            ->latest()
            ->limit(5)
            ->get();

        $todayPickups = Booking::with(['user', 'car.brand', 'status'])
            ->whereDate('pickup_at', $today)
            ->orderBy('pickup_at')
            ->get();

        $todayReturns = Booking::with(['user', 'car.brand', 'status'])
            ->whereDate('return_at', $today)
            ->orderBy('return_at')
            ->get();

        return view('staff.staffdashboard', [
            'pendingBookings' => $pendingBookings,
            'pendingPayments' => $pendingPayments,
            'activeRentals' => $activeRentals,
            'returnsToday' => $returnsToday,
            'attentionCars' => $attentionCars,
            'recentBookings' => $recentBookings,
            'todayPickups' => $todayPickups,
            'todayReturns' => $todayReturns,
        ]);
    }
}
