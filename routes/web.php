<?php

use App\Http\Controllers\CarsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;


Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
  
});



Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');

        Route::get('/cars', [AdminDashboardController::class, 'cars'])->name('cars');
        Route::post('/cars', [CarsController::class, 'store'])->name('cars.store');
        Route::get('/cars/{id}/edit', [CarsController::class, 'edit'])->name('cars.edit');
        Route::put('/cars/{id}', [CarsController::class, 'update'])->name('cars.update');
        Route::delete('/cars/{id}', [CarsController::class, 'destroy'])->name('cars.destroy');

        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/revenue', [AdminDashboardController::class, 'revenue'])->name('revenue');
    });
