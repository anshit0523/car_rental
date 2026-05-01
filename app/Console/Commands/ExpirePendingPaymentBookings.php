<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\PaymentStatus;
use App\Models\Status;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingPaymentBookings extends Command
{
    protected $signature = 'bookings:expire-pending-payments';

    protected $description = 'Expire bookings that are still pending payment after 24 hours.';

    public function handle(): int
    {
        $pendingPaymentStatus = Status::where('name', 'Pending Payment')->first();
        $cancelledStatus = Status::firstOrCreate(['name' => 'Cancelled']);

        $awaitingPaymentStatus = PaymentStatus::where('code', 'awaiting_payment')->first();
        $failedPaymentStatus = PaymentStatus::firstOrCreate(
            ['code' => 'failed'],
            ['name' => 'Failed']
        );

        if (!$pendingPaymentStatus || !$awaitingPaymentStatus) {
            $this->warn('Pending Payment or Awaiting Payment status was not found.');
            return self::SUCCESS;
        }

        $expiredBookings = Booking::with(['payments'])
            ->where('status_id', $pendingPaymentStatus->id)
            ->where('created_at', '<=', now()->subHours(24))
            ->whereHas('payments', function ($query) use ($awaitingPaymentStatus) {
                $query->whereNull('return_issue_id')
                    ->where('payment_status_id', $awaitingPaymentStatus->id);
            })
            ->get();

        $count = 0;

        DB::transaction(function () use (
            $expiredBookings,
            $cancelledStatus,
            $awaitingPaymentStatus,
            $failedPaymentStatus,
            &$count
        ) {
            foreach ($expiredBookings as $booking) {
                $booking->update([
                    'status_id' => $cancelledStatus->id,
                ]);

                $booking->payments()
                    ->whereNull('return_issue_id')
                    ->where('payment_status_id', $awaitingPaymentStatus->id)
                    ->update([
                        'payment_status_id' => $failedPaymentStatus->id,
                        'notes' => 'Payment expired because no receipt was submitted within 24 hours.',
                    ]);

                $count++;
            }
        });

        $this->info("Expired {$count} pending payment booking(s).");

        return self::SUCCESS;
    }
}