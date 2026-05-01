<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Booking;
use App\Models\Notification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| PICKUP REMINDER
|--------------------------------------------------------------------------
*/

Schedule::call(function () {

    $bookings = Booking::whereBetween('pickup_at', [
        now()->addHours(2)->subMinutes(5),
        now()->addHours(2)->addMinutes(5)
    ])
    ->whereDoesntHave('notifications', function ($q) {
        $q->where('type','pickup_2hour_reminder');
    })
    ->get();

    foreach ($bookings as $booking) {

        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'title' => 'Pickup Soon',
            'message' => 'Your car pickup is in 2 hours.',
            'type' => 'pickup_2hour_reminder',
            'link' => 'user/upcoming'
           
        ]);

    }

})->everyMinute();


/*
|--------------------------------------------------------------------------
| RETURN REMINDER
|--------------------------------------------------------------------------
*/

Schedule::call(function () {

    $bookings = Booking::whereDate('return_at', now()->addDay())
        ->whereDoesntHave('notifications', function ($q) {
            $q->where('type', 'return_reminder');
        })
        ->get();

    foreach ($bookings as $booking) {

        Notification::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'title' => 'Return Reminder',
            'message' => 'Reminder: Your car return is tomorrow.',
            'type' => 'return_reminder',
            'link' => 'user/active'
        
        ]);

    }

})->everyMinute();


Schedule::command('traccar:sync-positions')->everyMinute();
Schedule::command('bookings:update-status')->everyMinute();
Schedule::command('bookings:expire-pending-payments')->everyMinute();