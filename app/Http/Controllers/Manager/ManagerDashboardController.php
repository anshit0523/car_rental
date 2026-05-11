<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $driver = DB::connection()->getDriverName();

        $currentMonthStr = now()->format('Y-m');
        $lastMonthStr = now()->copy()->startOfMonth()->subMonth()->format('Y-m');

        $startOfMonth = now()->copy()->startOfMonth();
        $endOfMonth = now()->copy()->endOfMonth();

        // 3 previous months + current month = 4 months total
        $startGraphMonth = now()->copy()->startOfMonth()->subMonths(3);
        $endGraphMonth = now()->copy()->endOfMonth();

        $totalBookings = Booking::whereBetween('pickup_at', [$startOfMonth, $endOfMonth])->count();
        $totalCars = Car::count();
        $totalUsers = User::count();

        $totalRevenue = Booking::whereBetween('pickup_at', [$startOfMonth, $endOfMonth])
            ->sum('total_price');

        $activeStatusId = DB::table('statuses')
            ->where('name', 'Active')
            ->value('id');

        $activeCars = $activeStatusId
            ? Booking::where('status_id', $activeStatusId)->count()
            : 0;

        if ($driver === 'pgsql') {
            $rawMonthlyData = DB::table('bookings')
                ->selectRaw("
                    TO_CHAR(pickup_at, 'YYYY-MM') as month_key,
                    COUNT(*) as bookings,
                    COALESCE(SUM(total_price), 0) as revenue
                ")
                ->whereBetween('pickup_at', [$startGraphMonth, $endGraphMonth])
                ->groupByRaw("TO_CHAR(pickup_at, 'YYYY-MM')")
                ->get()
                ->keyBy('month_key');

            $rawUserMonthlyData = DB::table('users')
                ->selectRaw("
                    TO_CHAR(created_at, 'YYYY-MM') as month_key,
                    COUNT(*) as users
                ")
                ->whereBetween('created_at', [$startGraphMonth, $endGraphMonth])
                ->groupByRaw("TO_CHAR(created_at, 'YYYY-MM')")
                ->get()
                ->keyBy('month_key');

            $bookingsThisMonth = DB::table('bookings')
                ->whereRaw("TO_CHAR(pickup_at, 'YYYY-MM') = ?", [$currentMonthStr])
                ->count();

            $bookingsLastMonth = DB::table('bookings')
                ->whereRaw("TO_CHAR(pickup_at, 'YYYY-MM') = ?", [$lastMonthStr])
                ->count();

            $revenueThisMonth = DB::table('bookings')
                ->whereRaw("TO_CHAR(pickup_at, 'YYYY-MM') = ?", [$currentMonthStr])
                ->sum('total_price') ?? 0;

            $revenueLastMonth = DB::table('bookings')
                ->whereRaw("TO_CHAR(pickup_at, 'YYYY-MM') = ?", [$lastMonthStr])
                ->sum('total_price') ?? 0;

            $usersThisMonth = User::whereRaw("TO_CHAR(created_at, 'YYYY-MM') = ?", [$currentMonthStr])
                ->count();

            $usersLastMonth = User::whereRaw("TO_CHAR(created_at, 'YYYY-MM') = ?", [$lastMonthStr])
                ->count();
        } else {
            $rawMonthlyData = DB::table('bookings')
                ->selectRaw('
                    DATE_FORMAT(pickup_at, "%Y-%m") as month_key,
                    COUNT(*) as bookings,
                    COALESCE(SUM(total_price), 0) as revenue
                ')
                ->whereBetween('pickup_at', [$startGraphMonth, $endGraphMonth])
                ->groupByRaw('DATE_FORMAT(pickup_at, "%Y-%m")')
                ->get()
                ->keyBy('month_key');

            $rawUserMonthlyData = DB::table('users')
                ->selectRaw('
                    DATE_FORMAT(created_at, "%Y-%m") as month_key,
                    COUNT(*) as users
                ')
                ->whereBetween('created_at', [$startGraphMonth, $endGraphMonth])
                ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
                ->get()
                ->keyBy('month_key');

            $bookingsThisMonth = DB::table('bookings')
                ->whereRaw('DATE_FORMAT(pickup_at, "%Y-%m") = ?', [$currentMonthStr])
                ->count();

            $bookingsLastMonth = DB::table('bookings')
                ->whereRaw('DATE_FORMAT(pickup_at, "%Y-%m") = ?', [$lastMonthStr])
                ->count();

            $revenueThisMonth = DB::table('bookings')
                ->whereRaw('DATE_FORMAT(pickup_at, "%Y-%m") = ?', [$currentMonthStr])
                ->sum('total_price') ?? 0;

            $revenueLastMonth = DB::table('bookings')
                ->whereRaw('DATE_FORMAT(pickup_at, "%Y-%m") = ?', [$lastMonthStr])
                ->sum('total_price') ?? 0;

            $usersThisMonth = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonthStr])
                ->count();

            $usersLastMonth = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$lastMonthStr])
                ->count();
        }

        $monthlyData = collect();

        for ($date = $startGraphMonth->copy(); $date <= now()->startOfMonth(); $date->addMonth()) {
            $key = $date->format('Y-m');

            $monthlyData->push((object) [
                'month_label' => $date->format('M Y'),
                'bookings' => $rawMonthlyData[$key]->bookings ?? 0,
                'revenue' => $rawMonthlyData[$key]->revenue ?? 0,
                'users' => $rawUserMonthlyData[$key]->users ?? 0,
            ]);
        }

        $months = $monthlyData->pluck('month_label')->toArray();
        $bookingsData = $monthlyData->pluck('bookings')->toArray();
        $revenueData = $monthlyData->pluck('revenue')->toArray();
        $usersData = $monthlyData->pluck('users')->toArray();

        $bookingsTrend = $bookingsLastMonth > 0
            ? round((($bookingsThisMonth - $bookingsLastMonth) / $bookingsLastMonth) * 100, 1)
            : 0;

        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        $usersTrend = $usersLastMonth > 0
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1)
            : 0;

        $statusDistribution = DB::table('bookings')
            ->join('statuses', 'bookings.status_id', '=', 'statuses.id')
            ->select('statuses.name', DB::raw('count(*) as count'))
            ->groupBy('statuses.name')
            ->orderBy('statuses.name')
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

        $revenueTrendIcon = $revenueTrend > 0 ? '↑' : ($revenueTrend < 0 ? '↓' : '');
        $revenueTrendColor = $revenueTrend > 0
            ? 'text-green-600'
            : ($revenueTrend < 0 ? 'text-red-600' : 'text-gray-600');

        return view('manager.managerdashboard', [
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
            'usersData' => $usersData,

            'statusLabels' => $statusLabels,
            'statusData' => $statusData,
            'recentBookings' => $recentBookings,

            'bookingsTrendIcon' => $bookingsTrendIcon,
            'bookingsTrendColor' => $bookingsTrendColor,
            'revenueTrendIcon' => $revenueTrendIcon,
            'revenueTrendColor' => $revenueTrendColor,
        ]);
    }
}