<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $currentMonthStr = now()->format('Y-m');
        $lastMonthStr = now()->startOfMonth()->subMonth()->format('Y-m');

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $totalBookings = Booking::whereBetween('pickup_at', [$startOfMonth, $endOfMonth])->count();
        $totalCars = Car::count();
        $activeCars = Booking::where('status_id', 2)->count();
        $totalUsers = User::count();
        $totalRevenue = Booking::whereBetween('pickup_at', [$startOfMonth, $endOfMonth])->sum('total_price');

        $monthlyData = DB::table('bookings')
            ->selectRaw("
                EXTRACT(YEAR FROM pickup_at) as year,
                EXTRACT(MONTH FROM pickup_at) as month_num,
                TO_CHAR(pickup_at, 'Mon YYYY') as month_label,
                COUNT(*) as bookings,
                COALESCE(SUM(total_price), 0) as revenue
            ")
            ->groupByRaw("
                EXTRACT(YEAR FROM pickup_at),
                EXTRACT(MONTH FROM pickup_at),
                TO_CHAR(pickup_at, 'Mon YYYY')
            ")
            ->orderByRaw("
                EXTRACT(YEAR FROM pickup_at) DESC,
                EXTRACT(MONTH FROM pickup_at) DESC
            ")
            ->limit(8)
            ->get()
            ->reverse()
            ->values();

        $months = $monthlyData->pluck('month_label')->toArray();
        $bookingsData = $monthlyData->pluck('bookings')->toArray();
        $revenueData = $monthlyData->pluck('revenue')->toArray();

        $bookingsThisMonth = DB::table('bookings')
            ->whereRaw("TO_CHAR(pickup_at, 'YYYY-MM') = ?", [$currentMonthStr])
            ->count();

        $bookingsLastMonth = DB::table('bookings')
            ->whereRaw("TO_CHAR(pickup_at, 'YYYY-MM') = ?", [$lastMonthStr])
            ->count();

        $bookingsTrend = $bookingsLastMonth > 0
            ? round((($bookingsThisMonth - $bookingsLastMonth) / $bookingsLastMonth) * 100, 1)
            : 0;

        $revenueThisMonth = DB::table('bookings')
            ->whereRaw("TO_CHAR(pickup_at, 'YYYY-MM') = ?", [$currentMonthStr])
            ->sum('total_price') ?? 0;

        $revenueLastMonth = DB::table('bookings')
            ->whereRaw("TO_CHAR(pickup_at, 'YYYY-MM') = ?", [$lastMonthStr])
            ->sum('total_price') ?? 0;

        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        $revenueTrendIcon = $revenueTrend > 0 ? '↑' : ($revenueTrend < 0 ? '↓' : '');
        $revenueTrendColor = $revenueTrend > 0
            ? 'text-green-600'
            : ($revenueTrend < 0 ? 'text-red-600' : 'text-gray-600');

        $usersThisMonth = User::whereRaw("TO_CHAR(created_at, 'YYYY-MM') = ?", [$currentMonthStr])->count();
        $usersLastMonth = User::whereRaw("TO_CHAR(created_at, 'YYYY-MM') = ?", [$lastMonthStr])->count();

        $usersTrend = $usersLastMonth > 0
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1)
            : 0;

        $statusDistribution = DB::table('bookings')
            ->join('statuses', 'bookings.status_id', '=', 'statuses.id')
            ->select('statuses.name', DB::raw('count(*) as count'))
            ->groupBy('statuses.name')
            ->get();

        $statusLabels = $statusDistribution->pluck('name')->toArray();
        $statusData = $statusDistribution->pluck('count')->toArray();

        $recentBookings = Booking::with(['user', 'car.brand', 'status'])
            ->latest()
            ->limit(5)
            ->get();

        $bookingsTrendIcon = $bookingsTrend > 0 ? '↑' : ($bookingsTrend < 0 ? '↓' : '');
        $bookingsTrendColor = $bookingsTrend > 0
            ? 'text-green-600'
            : ($bookingsTrend < 0 ? 'text-red-600' : 'text-gray-600');

        return view('admin.admindashboard', [
            'totalBookings' => $totalBookings,
            'totalCars' => $totalCars,
            'activeCars' => $activeCars,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'bookingsTrend' => $bookingsTrend,
            'revenueTrend' => $revenueTrend,
            'usersTrend' => $usersTrend,
            'months' => $months,
            'bookingsData' => $bookingsData,
            'revenueData' => $revenueData,
            'statusLabels' => $statusLabels,
            'statusData' => $statusData,
            'recentBookings' => $recentBookings,
            'bookingsTrendIcon' => $bookingsTrendIcon,
            'bookingsTrendColor' => $bookingsTrendColor,
            'revenueTrendIcon' => $revenueTrendIcon,
            'revenueTrendColor' => $revenueTrendColor,
        ]);
    }

    public function users()
    {
        $users = User::with('role')->paginate(15);

        return view('admin.adminuser', [
            'users' => $users,
        ]);
    }

    public function revenue()
    {
        $monthlyRevenue = DB::table('bookings')
            ->selectRaw("
                EXTRACT(YEAR FROM pickup_at) as year,
                EXTRACT(MONTH FROM pickup_at) as month_num,
                TO_CHAR(MIN(pickup_at), 'Mon YYYY') as month_label,
                COALESCE(SUM(total_price), 0) as total
            ")
            ->groupByRaw("
                EXTRACT(YEAR FROM pickup_at),
                EXTRACT(MONTH FROM pickup_at)
            ")
            ->orderByRaw("
                EXTRACT(YEAR FROM pickup_at) DESC,
                EXTRACT(MONTH FROM pickup_at) DESC
            ")
            ->limit(6)
            ->get();

        $revenueWithGrowth = $monthlyRevenue->map(function ($row, $index) use ($monthlyRevenue) {
            $prevTotal = $monthlyRevenue->get($index + 1)?->total ?? null;

            $row->growth = ($prevTotal && $prevTotal > 0)
                ? round((($row->total - $prevTotal) / $prevTotal) * 100, 1)
                : null;

            return $row;
        });

        return view('admin.adminrevenue', [
            'monthlyRevenue' => $revenueWithGrowth,
        ]);
    }
}