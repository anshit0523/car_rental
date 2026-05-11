<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarType;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $cars = Car::with(['brand', 'transmission', 'carType'])
            ->where('active', true)
            ->take(6)
            ->get();

        $carTypes = CarType::all();

        return view('landing', compact('cars', 'carTypes'));
    }

    public function apiLandingCars()
    {
        $cars = Car::with(['brand', 'transmission', 'carType'])
            ->where('active', true)
            ->take(6)
            ->get();

        return response()->json([
            'success' => true,
            'cars' => $cars,
        ]);
    }
}