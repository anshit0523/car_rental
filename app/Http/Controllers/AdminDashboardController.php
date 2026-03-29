<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckRole;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use App\Models\FuelType;
use App\Models\Tracker;
use App\Models\Transmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{


    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $currentMonthStr = Carbon::now()->format('Y-m');
        $lastMonthStr = Carbon::create(Carbon::now()->year, Carbon::now()->month, 1)
            ->subMonth()
            ->format('Y-m');

        $totalBookings = Booking::whereBetween('pickup_at', [$startOfMonth, $endOfMonth])->count();
        $totalCars = Car::count();
        $activeCars = Booking::where('status_id', 2)->count();
        $totalUsers = User::count();
        $totalRevenue = Booking::whereBetween('pickup_at', [$startOfMonth, $endOfMonth])
            ->sum('total_price');

        $monthlyData = DB::table('bookings')
            ->selectRaw('YEAR(pickup_at) as year, MONTH(pickup_at) as month_num, DATE_FORMAT(pickup_at, "%b %Y") as month_label, COUNT(*) as bookings, COALESCE(SUM(total_price), 0) as revenue')
            ->groupByRaw('YEAR(pickup_at), MONTH(pickup_at), DATE_FORMAT(pickup_at, "%b %Y")')
            ->orderByRaw('YEAR(pickup_at) DESC, MONTH(pickup_at) DESC')
            ->limit(8)
            ->get()
            ->reverse()
            ->values();

        $months = $monthlyData->pluck('month_label')->toArray();
        $bookingsData = $monthlyData->pluck('bookings')->toArray();
        $revenueData = $monthlyData->pluck('revenue')->toArray();

        $bookingsThisMonth = DB::table('bookings')
            ->whereRaw('DATE_FORMAT(pickup_at, "%Y-%m") = ?', [$currentMonthStr])
            ->count();

        $bookingsLastMonth = DB::table('bookings')
            ->whereRaw('DATE_FORMAT(pickup_at, "%Y-%m") = ?', [$lastMonthStr])
            ->count();

        $bookingsTrend = $bookingsLastMonth > 0
            ? round((($bookingsThisMonth - $bookingsLastMonth) / $bookingsLastMonth) * 100, 1)
            : 0;

        $revenueThisMonth = DB::table('bookings')
            ->whereRaw('DATE_FORMAT(pickup_at, "%Y-%m") = ?', [$currentMonthStr])
            ->sum('total_price') ?? 0;
       


        $revenueLastMonth = DB::table('bookings')
            ->whereRaw('DATE_FORMAT(pickup_at, "%Y-%m") = ?', [$lastMonthStr])
            ->sum('total_price') ?? 0;

        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        $revenueTrendIcon = $revenueTrend > 0 ? '↑' : ($revenueTrend < 0 ? '↓' : '');
        $revenueTrendColor = $revenueTrend > 0
            ? 'text-green-600'
            : ($revenueTrend < 0 ? 'text-red-600' : 'text-gray-600');

        $usersThisMonth = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonthStr])->count();
        $usersLastMonth = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$lastMonthStr])->count();
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

    public function cars()
    {
        $brands = Brand::all();
        $transmissions = Transmission::all();
        $fuelTypes = FuelType::all();
        $trackers = Tracker::orderBy('imei')->get();

        $cars = Car::with(['brand', 'transmission', 'fuelType', 'tracker'])
            ->withCount('bookings')
            ->paginate(9);

        return view('admin.admincars', compact(
            'brands',
            'transmissions',
            'fuelTypes',
            'trackers',
            'cars'
        ));
    }



    public function users()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.adminuser', ['users' => $users]);
    }

    public function revenue()
    {
        $monthlyRevenue = DB::table('bookings')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key, SUM(total_price) as total')
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
            ->orderBy('month_key', 'desc')
            ->limit(6)
            ->get();

        // Calculate real month-over-month growth for each row
        $revenueWithGrowth = $monthlyRevenue->map(function ($row, $index) use ($monthlyRevenue) {
            // Previous month is the next item (since ordered desc)
            $prevTotal = $monthlyRevenue->get($index + 1)?->total ?? null;

            $growth = null;
            if ($prevTotal && $prevTotal > 0) {
                $growth = round((($row->total - $prevTotal) / $prevTotal) * 100, 1);
            }

            // Format month label e.g. "Jan 2025"
            $row->month_label = \Carbon\Carbon::createFromFormat('Y-m', $row->month_key)->format('M Y');
            $row->growth      = $growth;

            return $row;
        });

        return view('admin.adminrevenue', [
            'monthlyRevenue' => $revenueWithGrowth,
        ]);
    }
}
