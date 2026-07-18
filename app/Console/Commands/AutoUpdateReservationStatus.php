<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class AutoUpdateReservationStatus extends Command
{
    protected $signature = 'reservations:auto-update-status';
    protected $description = 'Mark past confirmed reservations as no_show';

    public function handle(): int
    {
        $count = Reservation::where('status', 'confirmed')
            ->whereDate('check_in_date', '<', today())
            ->update([
                'status' => 'no_show',
                'updated_at' => now(),
            ]);

        $this->info("{$count} reservation(s) marked as no_show.");

        return self::SUCCESS;
    }
}
