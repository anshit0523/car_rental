<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class LandingController extends Controller
{
public function index()
    {
        // Show available cars only
        $cars = Car::where('active', true)->take(6)->get();
        return view('landing', compact('cars'));
    }


    //api method to get featured cars for the landing page
    public function apiLandingCars()
{
    $cars = Car::with(['brand', 'transmission'])
        ->where('active', true)
        ->take(6)
        ->get();

    return response()->json([
        'success' => true,
        'cars' => $cars,
    ]);
}

}