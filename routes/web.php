<?php

use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AdminPaymentSettingController;
use App\Http\Controllers\AdminTrackerController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LiveMapController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReturnIssueController;
use App\Http\Controllers\Staff\StaffBookingController;
use App\Http\Controllers\Staff\StaffCalendarController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\StaffMapController;
use App\Http\Controllers\Staff\StaffPaymentController;
use App\Http\Controllers\UserBookingController;
use App\Http\Controllers\UserCarBrowseController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserRentalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');

        // notification routes
        Route::get('/notifications/read/{id}', [UserDashboardController::class, 'markRead'])
            ->name('notifications.read');

        Route::get('/notifications/latest', [UserDashboardController::class, 'latestNotifications'])
            ->name('notifications.latest');

        Route::get('/rentals', [UserDashboardController::class, 'rentals'])->name('rentals');

        Route::get('/browse', [UserCarBrowseController::class, 'index'])->name('browse');
        Route::get('/search', [UserCarBrowseController::class, 'search'])->name('search');


        //profile routes
        Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile');
        Route::post('/profile/update', [UserProfileController::class, 'updateInfo'])->name('profile.update');
        Route::post('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');

        // Booking routes
        Route::get('/car/{id}/detail', [UserCarBrowseController::class, 'show'])->name('cardetail');

        Route::post('/booking/create', [UserBookingController::class, 'store'])->name('booking.create');


        // Booking routes for different statuses
        Route::get('/rentals', function () {
            return redirect()->route('user.rentals.active');
        })->name('rentals.index');

        Route::get('/active', [UserRentalController::class, 'active'])->name('rentals.active');
        Route::get('/rentals/pending', [UserRentalController::class, 'pending'])->name('rentals.pending');
        Route::get('/upcoming', [UserRentalController::class, 'upcoming'])->name('rentals.upcoming');
        Route::get('/completed', [UserRentalController::class, 'completed'])->name('rentals.completed');
        Route::get('/cancelled', [UserRentalController::class, 'cancelled'])->name('rentals.cancelled');
        Route::post('/user/booking/{booking}/cancel', [UserRentalController::class, 'cancel'])->name('user.booking.cancel');
        Route::post('/booking/{booking}/cancel', [UserBookingController::class, 'cancel'])->name('booking.cancel');
     Route::get('/rentals/failed', [UserRentalController::class, 'failed'])
            ->name('rentals.failed');
        Route::get('/payments', [UserBookingController::class, 'showPayment'])->name('payments');



        Route::post('/payment/process', [PaymentController::class, 'process'])
            ->name('payment.process');
        Route::get('/my-bookings', [UserBookingController::class, 'myBookings'])->name('my-bookings');

        Route::get('/booking/{id}/confirmation', [UserBookingController::class, 'confirmation'])->name('booking.confirmation');


        // Receipt routes
        Route::get('/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
        Route::get('/receipts/{receipt_id}', [ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/receipts/{receipt_id}/download', [ReceiptController::class, 'download'])->name('receipts.download');
        Route::post('/receipts/{receipt_id}/send', [ReceiptController::class, 'send'])->name('receipts.send');

        // AJAX routes
        Route::get('/api/car/{id}/details', [UserBookingController::class, 'getCarDetails'])->name('api.car-details');
        Route::post('/api/booking/check-availability', [AvailabilityController::class, 'check'])->name('api.check-availability');
        Route::get('/api/cars/{carId}/unavailable-dates', [AvailabilityController::class, 'unavailableDates'])->name('unavailable-dates');

        Route::get('/car/{id}', [UserCarBrowseController::class, 'show'])->name('car-detail');
        Route::get('/filter-cars', [UserCarBrowseController::class, 'filterCars'])->name('filter-cars');

        Route::get('/history', [UserDashboardController::class, 'history'])->name('history');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::get('/settings', [UserDashboardController::class, 'settings'])->name('settings');

     Route::get('/return-issues/{returnIssue}', [ReturnIssueController::class, 'show'])
            ->name('return-issues.show');

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
         Route::get('/bookings/{booking}/json', [AdminBookingController::class, 'showJson'])
        ->name('admin.bookings.showJson');

        Route::put('/bookings/{booking}/update-status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');
        Route::post('/bookings/{id}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');

        Route::get('/calendar', [AdminBookingController::class, 'calendar'])->name('calendar');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'showJson'])->name('bookings.json');
        // Car management routes

        Route::get('/cars', [CarsController::class, 'cars'])->name('cars');
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

        // adminPayment routes
        Route::get('/payments', [AdminPaymentController::class, 'index'])
            ->name('payments.index');

        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])
            ->name('payments.show');

        Route::get('/payments/export/csv', [AdminPaymentController::class, 'export'])
            ->name('payments.export');

        Route::post('/payments/{id}/approve', [AdminPaymentController::class, 'approve'])
            ->name('payments.approve');

        Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'reject'])
            ->name('payments.reject');


        // adminPaymentSettings routes
        Route::get('/payment-settings', [AdminPaymentSettingController::class, 'edit'])->name('payment-settings.edit');
        Route::post('/payment-settings', [AdminPaymentSettingController::class, 'update'])->name('payment-settings.update');

        //map route
        Route::get('/live-map', [LiveMapController::class, 'page'])->name('live-map');
        Route::get('/replay', [LiveMapController::class, 'replayPage'])->name('replay');
        Route::get('/replay/history', [LiveMapController::class, 'history'])->name('replay.history');
        // json endpoint (protected)
        Route::get('/live/positions', [LiveMapController::class, 'positions'])->name('live.positions');

        Route::get('/trackers/create', [AdminTrackerController::class, 'create'])->name('trackers.create');
        Route::post('/trackers', [AdminTrackerController::class, 'store'])->name('trackers.store');

  


    Route::get('/bookings/{booking}/return-issue/create', [ReturnIssueController::class, 'create'])
            ->name('return-issues.create');

        Route::post('/bookings/{booking}/return-issue', [ReturnIssueController::class, 'store'])
            ->name('return-issues.store');
;
    });




Route::middleware(['auth', 'staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [StaffBookingController::class, 'index'])->name('bookings.index');
        Route::put('/bookings/{booking}/status', [StaffBookingController::class, 'updateStatus'])->name('bookings.update-status');
        Route::get('/payments', [StaffPaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/approve', [StaffPaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [StaffPaymentController::class, 'reject'])->name('payments.reject');
        Route::get('/calendar', [StaffCalendarController::class, 'index'])->name('calendar');
        Route::get('/bookings/{booking}/json', [StaffCalendarController::class, 'showBookingJson'])->name('bookings.show-json');
        Route::get('/live-map', [StaffMapController::class, 'index'])->name('live-map');
        Route::get('/live/positions', [StaffMapController::class, 'positions'])->name('live.positions');
        Route::get('/replay', [StaffMapController::class, 'replayPage'])->name('replay');
        Route::get('/replay/history', [StaffMapController::class, 'history'])->name('replay.history');




    });
