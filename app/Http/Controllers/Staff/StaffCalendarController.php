<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StaffCalendarController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'weekly');
        $dateString = $request->get('date');

        $startDate = $dateString
            ? Carbon::parse($dateString)
            : now();

        $startDate->startOfDay();

        if ($view === '30days') {
            $endDate = $startDate->copy()->addDays(29);
        } else {
            $endDate = $startDate->copy()->addDays(6);
        }

        $calendarDates = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $calendarDates[] = [
                'full' => $current->copy(),
                'date' => $current->format('d'),
                'day' => $current->format('D'),
            ];
            $current->addDay();
        }

        $query = Car::with([
            'brand:id,name',
            'transmission:id,type',
            'fuelType',
            'bookings' => function ($q) use ($startDate, $endDate) {
                $q->whereHas('status', function ($s) {
                    $s->whereIn('name', [
                        'Pending',
                        'Confirmed',
                        'Reserved',
                        'Approved',
                        'Active',
                        'Pending Payment Verification',
                    ]);
                })
                ->where(function ($date) use ($startDate, $endDate) {
                    $date->whereBetween('pickup_at', [$startDate, $endDate])
                        ->orWhereBetween('return_at', [$startDate, $endDate])
                        ->orWhere(function ($overlap) use ($startDate, $endDate) {
                            $overlap->where('pickup_at', '<=', $startDate)
                                ->where('return_at', '>=', $endDate);
                        });
                })
                ->with('status')
                ->orderBy('pickup_at');
            }
        ])->where('active', true);

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->get('brand_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');

            $query->where(function ($q) use ($search) {
                $q->where('model', 'like', "%{$search}%");
            });
        }

        $cars = $query->paginate(5);

        $previousWeek = $startDate->copy()->subDays($view === '30days' ? 30 : 7)->format('Y-m-d');
        $nextWeek = $startDate->copy()->addDays($view === '30days' ? 30 : 7)->format('Y-m-d');

        $brands = Brand::all();
        $serviceTypes = DB::table('service_types')->orderBy('id')->get();
        $paymentMethods = PaymentMethods::orderBy('name')->get();

        $data = [
            'cars' => $cars,
            'calendarDates' => $calendarDates,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'previousWeek' => $previousWeek,
            'nextWeek' => $nextWeek,
            'brands' => $brands,
            'view' => $view,
            'serviceTypes' => $serviceTypes,
            'paymentMethods' => $paymentMethods,
        ];

        if ($request->ajax()) {
            return view('staff.staffcalendar', $data)->render();
        }

        return view('staff.staffcalendar', $data);
    }
}