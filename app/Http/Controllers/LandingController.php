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
}