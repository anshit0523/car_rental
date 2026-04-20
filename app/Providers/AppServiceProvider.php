<?php

namespace App\Providers;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $pendingPaymentsCount = 0;

            if (Auth::check() && in_array((int) Auth::user()->role_id, [1, 3])) {
                $pendingStatusId = DB::table('payment_statuses')
                    ->where('name', 'Pending')
                    ->value('id');

                if ($pendingStatusId) {
                    $pendingPaymentsCount = Payment::where('payment_status_id', $pendingStatusId)->count();
                }
            }

            $view->with('pendingPaymentsCount', $pendingPaymentsCount);
        });
    }
}