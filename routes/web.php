<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserBookingController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\PayPalPaymentController;
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
  
 // PayPal routes
    Route::get('/paypal/payment/{booking_id}', [App\Http\Controllers\PayPalPaymentController::class, 'createPayment'])->name('paypal.payment');
    Route::get('/paypal/success/{booking_id}', [App\Http\Controllers\PayPalPaymentController::class, 'success'])->name('paypal.success');
    Route::get('/paypal/cancel/{booking_id}', [App\Http\Controllers\PayPalPaymentController::class, 'cancel'])->name('paypal.cancel');
    Route::get('/my-bookings', [UserBookingController::class, 'myBookings'])->name('my-bookings');
    Route::post('/booking/{id}/cancel', [UserBookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('/booking/{id}/confirmation', [UserBookingController::class, 'confirmation'])->name('booking.confirmation');
    

    // Receipt routes
    Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{receipt_id}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt_id}/download', [ReceiptController::class, 'download'])->name('receipts.download');
    Route::post('/receipts/{receipt_id}/send', [ReceiptController::class, 'send'])->name('receipts.send');
    
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
        


       // Booking routes
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create/{car}', [AdminBookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');

        Route::put('/bookings/{booking}/update-status', [AdminBookingController::class, 'updateStatus']) ->name('bookings.updateStatus');
        Route::post('/bookings/{id}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/cars', [AdminDashboardController::class, 'cars'])->name('cars');
       
        Route::post('/cars', [CarsController::class, 'store'])->name('cars.store');
        Route::get('/cars/{id}/edit', [CarsController::class, 'edit'])->name('cars.edit');
        Route::put('/cars/{id}', [CarsController::class, 'update'])->name('cars.update');
        Route::delete('/cars/{id}', [CarsController::class, 'destroy'])->name('cars.destroy');
        
        // AdminUsermanagement routes
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/revenue', [AdminDashboardController::class, 'revenue'])->name('revenue');
    });


