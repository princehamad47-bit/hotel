<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoMarkNoShowReservations extends Command
{
    protected $signature = 'reservations:auto-update-status';
    protected $description = 'Mark past confirmed reservations as no_show';

    public function handle(): int
    {
        $reservations = Reservation::with('reservationRooms.room')
            ->where('status', 'confirmed')
            ->whereDate('check_in_date', '<', today())
            ->get();

        $updated = 0;

        foreach ($reservations as $reservation) {
            DB::transaction(function () use ($reservation, &$updated) {
                $reservation->update([
                    'status' => 'no_show',
                ]);

                foreach ($reservation->reservationRooms as $reservationRoom) {
                    $room = $reservationRoom->room;

                    if ($room && !in_array($room->status, ['occupied', 'cleaning', 'maintenance'])) {
                        $room->update([
                            'status' => 'available',
                        ]);
                    }
                }

                $updated++;
            });
        }

        $this->info("{$updated} reservation(s) marked as no_show.");

        return self::SUCCESS;
    }
}
