<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Notification;
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

        $cancelledStatus = Status::firstOrCreate([
            'name' => 'Cancelled',
        ]);

        $awaitingPaymentStatus = PaymentStatus::where('code', 'awaiting_payment')->first();

        $failedPaymentStatus = PaymentStatus::firstOrCreate(
            ['code' => 'failed'],
            ['name' => 'Failed']
        );

        if (!$pendingPaymentStatus || !$awaitingPaymentStatus) {
            $this->warn('Pending Payment or Awaiting Payment status was not found.');
            return self::SUCCESS;
        }

        $expiredBookings = Booking::with([
                'payments',
                'car.brand',
            ])
            ->where('status_id', $pendingPaymentStatus->id)
            ->where('created_at', '<=', now()->subMinutes(15))
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
                /*
                |--------------------------------------------------------------------------
                | Cancel the unpaid booking
                |--------------------------------------------------------------------------
                */
                $booking->update([
                    'status_id' => $cancelledStatus->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Update the existing Awaiting Payment row
                | Do not create a new payment row.
                |--------------------------------------------------------------------------
                */
                $booking->payments()
                    ->whereNull('return_issue_id')
                    ->where('payment_status_id', $awaitingPaymentStatus->id)
                    ->update([
                        'payment_status_id' => $failedPaymentStatus->id,
                        'payment_date' => now(),
                        'verified_at' => now(),
                        'verified_by' => null,
                        'notes' => 'System expired this payment because no receipt was submitted within 24 hours.',
                    ]);

                if ((int) ($booking->points_used ?? 0) > 0) {
                    $alreadyReturned = DB::table('points_transactions')
                        ->where('booking_id', $booking->id)
                        ->where('note', 'Returned redeemed points from expired unpaid booking')
                        ->exists();

                    if (!$alreadyReturned) {
                        $userRow = DB::table('users')
                            ->where('id', $booking->user_id)
                            ->lockForUpdate()
                            ->first();

                        $pointsToReturn = (int) $booking->points_used;
                        $balanceBefore = (int) ($userRow->points_balance ?? 0);
                        $balanceAfter = $balanceBefore + $pointsToReturn;

                        DB::table('users')
                            ->where('id', $booking->user_id)
                            ->update([
                                'points_balance' => $balanceAfter,
                                'updated_at' => now(),
                            ]);

                        $returnTypeId = DB::table('points_transaction_types')
                            ->where('name', 'return')
                            ->value('id');

                        if (!$returnTypeId) {
                            $returnTypeId = DB::table('points_transaction_types')->insertGetId([
                                'name' => 'return',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        DB::table('points_transactions')->insert([
                            'user_id' => $booking->user_id,
                            'booking_id' => $booking->id,
                            'points_id' => $returnTypeId,
                            'points_change' => $pointsToReturn,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceAfter,
                            'note' => 'Returned redeemed points from expired unpaid booking',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $carName = trim(
                    (optional(optional($booking->car)->brand)->name ?? '') . ' ' .
                    (optional($booking->car)->model ?? '')
                );

                if ($carName === '') {
                    $carName = 'your selected vehicle';
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent duplicate expiration notifications
                |--------------------------------------------------------------------------
                */
                $alreadyNotified = Notification::where('booking_id', $booking->id)
                    ->where('user_id', $booking->user_id)
                    ->where('type', 'booking_payment_expired')
                    ->exists();

                if (!$alreadyNotified) {
                    $pointsMessage = (int) ($booking->points_used ?? 0) > 0
                        ? ' Your redeemed points have been returned to your account.'
                        : '';

                    Notification::create([
                        'user_id' => $booking->user_id,
                        'booking_id' => $booking->id,
                        'title' => 'Booking Expired',
                        'message' => 'Your booking for ' . $carName . ' was cancelled because no payment receipt was submitted within 24 hours.' . $pointsMessage,
                        'type' => 'booking_payment_expired',
                        'link' => 'user/cancelled',
                    ]);
                }

                $count++;
            }
        });

        $this->info("Expired {$count} pending payment booking(s).");

        return self::SUCCESS;
    }
}