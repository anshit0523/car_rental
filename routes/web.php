<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserCarBrowseController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminDashboardController;


Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth','user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

    Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/rentals', [UserDashboardController::class, 'rentals'])->name('rentals');

    Route::get('/browse', [UserCarBrowseController::class, 'index'])->name('browse');
    Route::post('/search', [UserCarBrowseController::class, 'search'])->name('search');
    Route::get('/car/{id}', [UserCarBrowseController::class, 'show'])->name('car-detail');
    Route::get('/filter-cars', [UserCarBrowseController::class, 'filterCars'])->name('filter-cars');

    Route::get('/history', [UserDashboardController::class, 'history'])->name('history');
    Route::get('/payments', [UserDashboardController::class, 'payments'])->name('payments');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::get('/settings', [UserDashboardController::class, 'settings'])->name('settings');
    
    // Booking routes
    Route::get('/bookings/create/{car}', [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{id}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
  
});


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        
       
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');

        Route::get('/cars', [AdminDashboardController::class, 'cars'])->name('cars');
        Route::post('/cars', [CarsController::class, 'store'])->name('cars.store');
        Route::get('/cars/{id}/edit', [CarsController::class, 'edit'])->name('cars.edit');
        Route::put('/cars/{id}', [CarsController::class, 'update'])->name('cars.update');
        Route::delete('/cars/{id}', [CarsController::class, 'destroy'])->name('cars.destroy');

        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/revenue', [AdminDashboardController::class, 'revenue'])->name('revenue');
    });


