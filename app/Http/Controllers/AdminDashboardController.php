<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\User;
use App\Models\Brand;
use App\Models\Booking;

use App\Models\FuelType;
use App\Models\Transmission;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{


    public function index()
    {
        // Dashboard statistics
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalBookings = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $totalCars = Car::count();
        $activeCars = Booking::where('status_id', 2)->count();
        $totalUsers = User::count();
        $totalRevenue = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_price');

        // Get last 4 months of data using database grouping (solves timezone issues)
        $monthlyData = DB::table('bookings')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as bookings, SUM(total_price) as revenue')
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
            ->orderBy('month', 'asc')
            ->limit(8)
            ->get();


        // Extract month labels and data
        $months = $monthlyData->pluck('month')->map(function ($month) {
            return Carbon::createFromFormat('Y-m', $month)->format('M');
        })->toArray();

        $bookingsData = $monthlyData->pluck('bookings')->toArray();
        $revenueData = $monthlyData->pluck('revenue')->toArray();

        // Calculate trends for this month vs last month
        $currentMonthStr = Carbon::now()->format('Y-m');
        $lastMonthStr = Carbon::now()->subMonth()->format('Y-m');

        $bookingsThisMonth = DB::table('bookings')
            ->selectRaw('COUNT(*) as count')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonthStr])
            ->value('count') ?? 0;

        $bookingsLastMonth = DB::table('bookings')
            ->selectRaw('COUNT(*) as count')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$lastMonthStr])
            ->value('count') ?? 0;

        $bookingsTrend = $bookingsLastMonth > 0 ? round((($bookingsThisMonth - $bookingsLastMonth) / $bookingsLastMonth) * 100, 1) : 0;

        $revenueThisMonth = DB::table('bookings')
            ->selectRaw('SUM(total_price) as total')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonthStr])
            ->value('total') ?? 0;

        $revenueLastMonth = DB::table('bookings')
            ->selectRaw('SUM(total_price) as total')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$lastMonthStr])
            ->value('total') ?? 0;

        $revenueTrend = $revenueLastMonth > 0 ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) : 0;

        $revenueTrendIcon = $revenueTrend > 0 ? '↑' : ($revenueTrend < 0 ? '↓' : '');
        $revenueTrendColor = $revenueTrend > 0
            ? 'text-green-600'
            : ($revenueTrend < 0 ? 'text-red-600' : 'text-gray-600');

        $usersThisMonth = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonthStr])->count();
        $usersLastMonth = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$lastMonthStr])->count();
        $usersTrend = $usersLastMonth > 0 ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1) : 0;

        // Status distribution
        $statusDistribution = DB::table('bookings')
            ->join('statuses', 'bookings.status_id', '=', 'statuses.id')
            ->select('statuses.name', DB::raw('count(*) as count'))
            ->groupBy('statuses.name')
            ->get();

        $statusLabels = $statusDistribution->pluck('name')->toArray();
        $statusData = $statusDistribution->pluck('count')->toArray();

        // Recent bookings
        $recentBookings = Booking::with(['user', 'car.brand', 'status'])
            ->latest()
            ->limit(5)
            ->get();

        $bookingsTrendIcon = $bookingsTrend > 0 ? '↑' : ($bookingsTrend < 0 ? '↓' : '');
        $bookingsTrendColor = $bookingsTrend > 0 ? 'text-green-600' : ($bookingsTrend < 0 ? 'text-red-600' : 'text-gray-600');


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
        $cars = Car::with(['brand', 'transmission', 'fuelType'])
            ->withCount(['bookings'])
            ->paginate(9);

        $brands = Brand::all();
        $transmissions = Transmission::all();
        $fuelTypes = FuelType::all();

        return view('admin.admincars', compact('cars', 'brands', 'transmissions', 'fuelTypes'));
    }



    public function users()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.adminuser', ['users' => $users]);
    }

    public function revenue()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $monthlyRevenue = DB::table('bookings')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total')
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
            ->orderBy('month', 'desc')
            ->limit(5)
            ->get();

        return view('admin.adminrevenue', ['monthlyRevenue' => $monthlyRevenue]);
    }
}
