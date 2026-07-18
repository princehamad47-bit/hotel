@extends('layouts.app')

@section('content')
<h1 class="page-title">Hotel Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Today Arrivals</div>
        <div class="stat-value">{{ $stats['today_arrivals'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Today Departures</div>
        <div class="stat-value">{{ $stats['today_departures'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Check-Ins Done Today</div>
        <div class="stat-value">{{ $stats['today_checkins_done'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Check-Outs Done Today</div>
        <div class="stat-value">{{ $stats['today_checkouts_done'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Available Rooms</div>
        <div class="stat-value">{{ $stats['available_rooms'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Occupied Rooms</div>
        <div class="stat-value">{{ $stats['occupied_rooms'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Cleaning Rooms</div>
        <div class="stat-value">{{ $stats['cleaning_rooms'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Maintenance Rooms</div>
        <div class="stat-value">{{ $stats['maintenance_rooms'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Total Guests</div>
        <div class="stat-value">{{ $stats['total_guests'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Total Reservations</div>
        <div class="stat-value">{{ $stats['total_reservations'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">In-House Guests</div>
        <div class="stat-value">{{ $stats['checked_in_reservations'] }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-title">Pending Balance</div>
        <div class="stat-value">{{ number_format($stats['pending_balance'], 2) }}</div>
    </div>
</div>

<div class="card">
    <h3 class="section-title">Quick Actions</h3>
    <div class="quick-links">
        <a href="{{ route('reservations.create') }}" class="btn btn-primary">New Reservation</a>
        <a href="{{ route('guests.create') }}" class="btn btn-success">Add Guest</a>
        <a href="{{ route('rooms.index') }}" class="btn btn-warning">Open Rooms</a>
        <a href="{{ route('services.create') }}" class="btn btn-secondary">Add Service</a>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h3 class="section-title">Today's Arrivals</h3>

        @if ($todayArrivals->count())
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Reservation</th>
                        <th>Guest</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($todayArrivals as $item)
                    <tr>
                        <td>{{ $item->reservation_code }}</td>
                        <td>{{ $item->guest->first_name }} {{ $item->guest->last_name }}</td>
                        <td>
                            <span class="badge badge-{{ $item->status }}">
                                {{ str_replace('_', ' ', ucfirst($item->status)) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="muted">No arrivals for today.</p>
        @endif
    </div>

    <div class="card">
        <h3 class="section-title">Today's Departures</h3>

        @if ($todayDepartures->count())
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Reservation</th>
                        <th>Guest</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($todayDepartures as $item)
                    <tr>
                        <td>{{ $item->reservation_code }}</td>
                        <td>{{ $item->guest->first_name }} {{ $item->guest->last_name }}</td>
                        <td>
                            <span class="badge badge-{{ $item->status }}">
                                {{ str_replace('_', ' ', ucfirst($item->status)) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="muted">No departures for today.</p>
        @endif
    </div>
</div>

<div class="card">
    <h3 class="section-title">In-House Guests</h3>

    @if ($inHouseGuests->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reservation</th>
                    <th>Guest</th>
                    <th>Check In Date</th>
                    <th>Check Out Date</th>
                    <th>Checked In At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inHouseGuests as $item)
                <tr>
                    <td>{{ $item->reservation_code }}</td>
                    <td>{{ $item->guest->first_name }} {{ $item->guest->last_name }}</td>
                    <td>{{ $item->check_in_date->format('Y-m-d') }}</td>
                    <td>{{ $item->check_out_date->format('Y-m-d') }}</td>
                    <td>{{ $item->checked_in_at ? $item->checked_in_at->format('Y-m-d H:i') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="muted">No guests are currently checked in.</p>
    @endif
</div>

<div class="card">
    <h3 class="section-title">Recent Reservations</h3>

    @if ($recentReservations->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reservation Code</th>
                    <th>Guest</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentReservations as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_code }}</td>
                    <td>{{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</td>
                    <td>{{ $reservation->check_in_date->format('Y-m-d') }}</td>
                    <td>{{ $reservation->check_out_date->format('Y-m-d') }}</td>
                    <td>
                        <span class="badge badge-{{ $reservation->status }}">
                            {{ str_replace('_', ' ', ucfirst($reservation->status)) }}
                        </span>
                    </td>
                    <td>{{ number_format($reservation->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="muted">No reservations found yet.</p>
    @endif
</div>

@auth
<div class="card">
    <p><strong>Logged in user:</strong> {{ auth()->user()->name }}</p>
    <p><strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}</p>
</div>
@endauth
@endsection