<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Status;
use Carbon\Carbon;

class UpdateBookingStatuses extends Command
{
    protected $signature = 'bookings:update-status';
    protected $description = 'Auto update booking statuses based on pickup and return time';

    public function handle()
{
    $now = now();

    $pendingIds = Status::whereIn('name', ['Reserved', 'Confirmed'])->pluck('id')->filter()->values();
    $activeId = Status::where('name', 'Active')->value('id');
    $returnId = Status::where('name', 'Return')->value('id');

    if ($pendingIds->isEmpty() || !$activeId || !$returnId) {
        $this->error('Required statuses not found.');
        return Command::FAILURE;
    }

    $toActive = Booking::whereIn('status_id', $pendingIds)
        ->where('pickup_at', '<=', $now)
        ->where('return_at', '>', $now)
        ->update([
            'status_id' => $activeId,
            'updated_at' => $now,
        ]);

    $toReturnFromPending = Booking::whereIn('status_id', $pendingIds)
        ->where('return_at', '<=', $now)
        ->update([
            'status_id' => $returnId,
            'updated_at' => $now,
        ]);

    $toReturnFromActive = Booking::where('status_id', $activeId)
        ->where('return_at', '<=', $now)
        ->update([
            'status_id' => $returnId,
            'updated_at' => $now,
        ]);

    $this->info("Updated {$toActive} booking(s) to Active.");
    $this->info("Updated {$toReturnFromPending} pending booking(s) to Return.");
    $this->info("Updated {$toReturnFromActive} active booking(s) to Return.");

    return Command::SUCCESS;
}
}