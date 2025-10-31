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
        // All logic stays the same — no need for manual role check here anymore
        $totalBookings = Booking::count();
        $totalCars = Car::count();
        $activeCars = Booking::where('status_id', 2)->count();
        $totalUsers = User::count();

        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $bookingsThisMonth = Booking::where('created_at', '>=', $currentMonth)->count();
        $bookingsLastMonth = Booking::whereBetween('created_at', [$lastMonth, $currentMonth])->count();
        $bookingsTrend = $bookingsLastMonth > 0 ? round((($bookingsThisMonth - $bookingsLastMonth) / $bookingsLastMonth) * 100, 1) : 0;

        $revenueThisMonth = Booking::where('created_at', '>=', $currentMonth)->sum('total_price');
        $revenueLastMonth = Booking::whereBetween('created_at', [$lastMonth, $currentMonth])->sum('total_price');
        $revenueTrend = $revenueLastMonth > 0 ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) : 0;

        $usersThisMonth = User::where('created_at', '>=', $currentMonth)->count();
        $usersLastMonth = User::whereBetween('created_at', [$lastMonth, $currentMonth])->count();
        $usersTrend = $usersLastMonth > 0 ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1) : 0;

        $totalRevenue = Booking::sum('total_price');

        $months = [];
        $bookingsData = [];
        $revenueData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');

            $monthStart = $date->startOfMonth();
            $monthEnd = $date->endOfMonth();

            $bookingsData[] = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $revenueData[] = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_price');
        }

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
        return view('admin.users', ['users' => $users]);
    }

    public function revenue()
    {
        $monthlyRevenue = DB::table('bookings')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total')
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('admin.revenue', ['monthlyRevenue' => $monthlyRevenue]);
    }
}
