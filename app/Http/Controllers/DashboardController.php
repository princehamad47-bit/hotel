<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'total_guests' => Guest::count(),
            'total_room_types' => RoomType::count(),
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'maintenance_rooms' => Room::where('status', 'maintenance')->count(),
            'cleaning_rooms' => Room::where('status', 'cleaning')->count(),
            'total_reservations' => Reservation::count(),
            'confirmed_reservations' => Reservation::where('status', 'confirmed')->count(),
            'checked_in_reservations' => Reservation::where('status', 'checked_in')->count(),
            'checked_out_reservations' => Reservation::where('status', 'checked_out')->count(),
            'cancelled_reservations' => Reservation::where('status', 'cancelled')->count(),
            'today_arrivals' => Reservation::whereDate('check_in_date', $today)->count(),
            'today_departures' => Reservation::whereDate('check_out_date', $today)->count(),
            'today_checkins_done' => Reservation::whereDate('checked_in_at', $today)->count(),
            'today_checkouts_done' => Reservation::whereDate('checked_out_at', $today)->count(),
            'pending_balance' => Reservation::sum(DB::raw('total_amount - paid_amount')),
        ];

        $recentReservations = Reservation::with('guest')
            ->latest()
            ->take(5)
            ->get();

        $todayArrivals = Reservation::with('guest')
            ->whereDate('check_in_date', $today)
            ->orderBy('check_in_date')
            ->get();

        $todayDepartures = Reservation::with('guest')
            ->whereDate('check_out_date', $today)
            ->orderBy('check_out_date')
            ->get();

        $inHouseGuests = Reservation::with('guest')
            ->where('status', 'checked_in')
            ->orderByDesc('checked_in_at')
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentReservations',
            'todayArrivals',
            'todayDepartures',
            'inHouseGuests'
        ));
    }
}
