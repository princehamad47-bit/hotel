@extends('layouts.app')

@section('content')
<h1 class="page-title">Reports</h1>

<p class="muted" style="margin-bottom: 20px;">
    Current report type:
    <strong>
        @if (($reportType ?? 'check_in_date') === 'booking_date')
        Booking Date
        @elseif (($reportType ?? 'check_in_date') === 'check_out_date')
        Check Out Date
        @else
        Check In Date
        @endif
    </strong>
</p>

<div class="card no-print">
    <form method="GET" action="{{ route('reports.index') }}">
        <div class="grid-2">
            <div class="form-group">
                <label>Report Type</label>
                <select name="report_type">
                    <option value="booking_date" @selected(($reportType ?? 'check_in_date' )=='booking_date' )>Booking Date</option>
                    <option value="check_in_date" @selected(($reportType ?? 'check_in_date' )=='check_in_date' )>Check In Date</option>
                    <option value="check_out_date" @selected(($reportType ?? 'check_in_date' )=='check_out_date' )>Check Out Date</option>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Status</option>
                    <option value="pending" @selected(($status ?? '' )=='pending' )>Pending</option>
                    <option value="confirmed" @selected(($status ?? '' )=='confirmed' )>Confirmed</option>
                    <option value="checked_in" @selected(($status ?? '' )=='checked_in' )>Checked In</option>
                    <option value="checked_out" @selected(($status ?? '' )=='checked_out' )>Checked Out</option>
                    <option value="cancelled" @selected(($status ?? '' )=='cancelled' )>Cancelled</option>
                    <option value="no_show" @selected(($status ?? '' )=='no_show' )>No Show</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            </div>

            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Generate Report</button>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Reset</a>
        <button type="button" onclick="window.print()" class="btn btn-success">Print Report</button>
    </form>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Reservations</div>
        <div class="stat-value">{{ $stats['reservation_count'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Confirmed</div>
        <div class="stat-value">{{ $stats['confirmed_count'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Checked In</div>
        <div class="stat-value">{{ $stats['checked_in_count'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Checked Out</div>
        <div class="stat-value">{{ $stats['checked_out_count'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Cancelled</div>
        <div class="stat-value">{{ $stats['cancelled_count'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Room Revenue</div>
        <div class="stat-value">{{ number_format($stats['room_revenue'], 2) }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Service Revenue</div>
        <div class="stat-value">{{ number_format($stats['service_revenue'], 2) }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Paid Amount</div>
        <div class="stat-value">{{ number_format($stats['paid_amount'], 2) }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Pending Balance</div>
        <div class="stat-value">{{ number_format($stats['pending_balance'], 2) }}</div>
    </div>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h3 class="section-title" style="margin-bottom:0;">Reservations in Selected Range</h3>
        <button type="button" onclick="window.print()" class="btn btn-primary no-print">Print Report</button>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reservation Code</th>
                    <th>Guest</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Grand Total</th>
                    <th>Paid Amount</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $reservation)
                <tr>
                    <td>
                        @can('module-access', ['reservations', 'read'])
                        <a href="{{ route('reservations.show', $reservation) }}">
                            {{ $reservation->reservation_code }}
                        </a>
                        @else
                        {{ $reservation->reservation_code }}
                        @endcan
                    </td>
                    <td>{{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</td>
                    <td>{{ $reservation->check_in_date->format('Y-m-d') }}</td>
                    <td>{{ $reservation->check_out_date->format('Y-m-d') }}</td>
                    <td>
                        <span class="badge badge-{{ $reservation->status }}">
                            {{ str_replace('_', ' ', ucfirst($reservation->status)) }}
                        </span>
                    </td>
                    <td>{{ number_format($reservation->grand_total, 2) }}</td>
                    <td>{{ number_format($reservation->paid_amount, 2) }}</td>
                    <td>{{ $reservation->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">No reservations found for this date range.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="no-print" style="margin-top: 20px;">
        {{ $reservations->links() }}
    </div>
</div>
@endsection