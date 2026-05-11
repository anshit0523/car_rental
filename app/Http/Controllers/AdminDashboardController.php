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
        $lastMonthStr = now()->copy()->startOfMonth()->subMonth()->format('Y-m');

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Show only previous 3 months + current month
        $startGraphMonth = now()->copy()->startOfMonth()->subMonths(3);
        $endGraphMonth = now()->copy()->endOfMonth();

        $totalBookings = Booking::whereBetween('pickup_at', [$startOfMonth, $endOfMonth])->count();
        $totalCars = Car::count();
        $activeCars = Booking::where('status_id', 2)->count();
        $totalUsers = User::count();
        $totalRevenue = Booking::whereBetween('pickup_at', [$startOfMonth, $endOfMonth])->sum('total_price');

        /*
        |--------------------------------------------------------------------------
        | Monthly Bookings and Revenue Graph
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Monthly Users Graph
        |--------------------------------------------------------------------------
        */
        $rawUserMonthlyData = DB::table('users')
            ->selectRaw("
                TO_CHAR(created_at, 'YYYY-MM') as month_key,
                COUNT(*) as users
            ")
            ->whereBetween('created_at', [$startGraphMonth, $endGraphMonth])
            ->groupByRaw("TO_CHAR(created_at, 'YYYY-MM')")
            ->get()
            ->keyBy('month_key');

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

        /*
        |--------------------------------------------------------------------------
        | Trends
        |--------------------------------------------------------------------------
        */
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

        $revenueTrendIcon = $revenueTrend > 0 ? '↑' : ($revenueTrend < 0 ? '↓' : '');
        $revenueTrendColor = $revenueTrend > 0
            ? 'text-green-600'
            : ($revenueTrend < 0 ? 'text-red-600' : 'text-gray-600');

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

    public function users()
    {
        $users = User::with('role')->paginate(15);

        return view('admin.adminuser', [
            'users' => $users,
        ]);
    }

    public function revenue()
{
    $startGraphMonth = now()->copy()->startOfMonth()->subMonths(5);
    $endGraphMonth = now()->copy()->endOfMonth();

    $rawRevenue = DB::table('bookings')
        ->selectRaw("
            TO_CHAR(pickup_at, 'YYYY-MM') as month_key,
            COALESCE(SUM(total_price), 0) as total
        ")
        ->whereBetween('pickup_at', [$startGraphMonth, $endGraphMonth])
        ->groupByRaw("TO_CHAR(pickup_at, 'YYYY-MM')")
        ->get()
        ->keyBy('month_key');

    $monthlyRevenue = collect();

    for ($date = $startGraphMonth->copy(); $date <= now()->startOfMonth(); $date->addMonth()) {
        $key = $date->format('Y-m');

        $monthlyRevenue->push((object) [
            'month_label' => $date->format('M Y'),
            'total' => $rawRevenue[$key]->total ?? 0,
        ]);
    }

    $chartMonthlyRevenue = $monthlyRevenue->map(function ($row, $index) use ($monthlyRevenue) {
        $prevTotal = $monthlyRevenue->get($index - 1)?->total ?? null;

        $row->growth = ($prevTotal && $prevTotal > 0)
            ? round((($row->total - $prevTotal) / $prevTotal) * 100, 1)
            : null;

        return $row;
    })->values();

    $revenueWithGrowth = $chartMonthlyRevenue->reverse()->values();

    return view('admin.adminrevenue', [
        'monthlyRevenue' => $revenueWithGrowth,
        'chartMonthlyRevenue' => $chartMonthlyRevenue,
    ]);
}

}