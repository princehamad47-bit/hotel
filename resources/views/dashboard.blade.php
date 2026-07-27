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
        @can('module-access', ['reservations', 'create'])
        <a href="{{ route('reservations.create') }}" class="btn btn-primary">New Reservation</a>
        @endcan
        @can('module-access', ['guests', 'create'])
        <a href="{{ route('guests.create') }}" class="btn btn-success">Add Guest</a>
        @endcan
        @can('module-access', ['rooms', 'read'])
        <a href="{{ route('rooms.index') }}" class="btn btn-warning">Open Rooms</a>
        @endcan
        @can('module-access', ['services', 'create'])
        <a href="{{ route('services.create') }}" class="btn btn-secondary">Add Service</a>
        @endcan
        @can('module-access', ['restaurant-orders', 'create'])
        <a href="{{ route('restaurant-orders.create') }}" class="btn btn-primary">Add Order</a>
        @endcan
        @can('module-access', ['menu-categories', 'create'])
        <a href="{{ route('menu-categories.create') }}" class="btn btn-success">Add Menu Categories</a>
        @endcan
        @can('module-access', ['menu-items', 'create'])
        <a href="{{ route('menu-items.create') }}" class="btn btn-warning">Add Menu Item</a>
        @endcan
    </div>
</div>

@can('module-access', ['reservations', 'read'])
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
                        <td>
                            <a href="{{ route('reservations.show', $item) }}">
                                {{ $item->reservation_code }}
                            </a>
                        </td>
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
                        <td>
                            <a href="{{ route('reservations.show', $item) }}">
                                {{ $item->reservation_code }}
                            </a>
                        </td>
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
                    <td>
                            <a href="{{ route('reservations.show', $item) }}">
                                {{ $item->reservation_code }}
                            </a>
                        </td>
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
                    <td>
                        <a href="{{ route('reservations.show', $reservation) }}">
                            {{ $reservation->reservation_code }}
                        </a>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="muted">No reservations found yet.</p>
    @endif
</div>
@endcan

@auth
<div class="card">
    <p><strong>Logged in user:</strong> {{ auth()->user()->name }}</p>
    <p><strong>Role:</strong> {{ ucfirst(auth()->user()->role?->name ?? 'User') }}</p>
</div>
@endauth
@endsection