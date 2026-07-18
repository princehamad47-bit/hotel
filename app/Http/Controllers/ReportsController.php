<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::today()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::today()->endOfMonth();

        $reportType = $request->get('report_type', 'check_in_date');
        $status = $request->get('status');

        $reservationsQuery = Reservation::with(['guest']);

        if ($reportType === 'booking_date') {
            $reservationsQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($reportType === 'check_out_date') {
            $reservationsQuery->whereBetween('check_out_date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ]);
        } else {
            $reservationsQuery->whereBetween('check_in_date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ]);
        }

        if (!empty($status)) {
            $reservationsQuery->where('status', $status);
        }

        $reservations = (clone $reservationsQuery)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $reservationIds = (clone $reservationsQuery)->pluck('id');

        $roomRevenue = Reservation::whereIn('id', $reservationIds)->sum('total_amount')
            - ReservationService::whereIn('reservation_id', $reservationIds)->sum('total_price');

        $serviceRevenue = ReservationService::whereIn('reservation_id', $reservationIds)->sum('total_price');

        $paidAmount = Payment::whereIn('reservation_id', $reservationIds)
            ->where('payment_status', 'paid')
            ->sum('amount');

        $pendingBalance = Reservation::whereIn('id', $reservationIds)
            ->sum(DB::raw('total_amount - paid_amount'));

        $stats = [
            'reservation_count' => Reservation::whereIn('id', $reservationIds)->count(),
            'confirmed_count' => Reservation::whereIn('id', $reservationIds)->where('status', 'confirmed')->count(),
            'checked_in_count' => Reservation::whereIn('id', $reservationIds)->where('status', 'checked_in')->count(),
            'checked_out_count' => Reservation::whereIn('id', $reservationIds)->where('status', 'checked_out')->count(),
            'cancelled_count' => Reservation::whereIn('id', $reservationIds)->where('status', 'cancelled')->count(),
            'room_revenue' => $roomRevenue,
            'service_revenue' => $serviceRevenue,
            'paid_amount' => $paidAmount,
            'pending_balance' => $pendingBalance,
        ];

        return view('reports.index', compact(
            'reservations',
            'stats',
            'startDate',
            'endDate',
            'reportType',
            'status'
        ));
    }
}
