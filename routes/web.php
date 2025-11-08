<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserBookingController;
use App\Http\Controllers\AdminBookingController;
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
    
    // Booking routes
    Route::get('/car/{id}/detail', [UserCarBrowseController::class, 'show'])->name('cardetail');
    Route::post('/booking/create', [UserBookingController::class, 'store'])->name('booking.create');
    Route::get('/payments', [UserBookingController::class, 'showPayment'])->name('payments');  
     Route::post('/payment/process', [UserBookingController::class, 'processPayment'])->name('payment.process'); 
    Route::get('/my-bookings', [UserBookingController::class, 'myBookings'])->name('my-bookings');
    Route::post('/booking/{id}/cancel', [UserBookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('/booking/{id}/confirmation', [UserBookingController::class, 'confirmation'])->name('booking.confirmation');
    
    // AJAX routes
    Route::get('/api/car/{id}/details', [UserBookingController::class, 'getCarDetails'])->name('api.car-details');
    Route::post('/api/booking/check-availability', [UserBookingController::class, 'checkAvailability'])->name('api.check-availability');
    Route::get('/car/{id}', [UserCarBrowseController::class, 'show'])->name('car-detail');
    Route::get('/filter-cars', [UserCarBrowseController::class, 'filterCars'])->name('filter-cars');

    Route::get('/history', [UserDashboardController::class, 'history'])->name('history');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::get('/settings', [UserDashboardController::class, 'settings'])->name('settings');
    
    
  
});


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        
       
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
// Booking routes
    Route::get('/bookings/create/{car}', [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{id}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/cars', [AdminDashboardController::class, 'cars'])->name('cars');
        Route::post('/cars', [CarsController::class, 'store'])->name('cars.store');
        Route::get('/cars/{id}/edit', [CarsController::class, 'edit'])->name('cars.edit');
        Route::put('/cars/{id}', [CarsController::class, 'update'])->name('cars.update');
        Route::delete('/cars/{id}', [CarsController::class, 'destroy'])->name('cars.destroy');

        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/revenue', [AdminDashboardController::class, 'revenue'])->name('revenue');
    });


