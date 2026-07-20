@extends('layouts.app')

@section('content')
<h1 class="page-title">Reservations</h1>

<div class="card">
    <form method="GET" action="{{ route('reservations.index') }}">
        <div class="grid-3">
            <div class="form-group">
                <label>Reservation Code</label>
                <input type="text" name="reservation_code" value="{{ request('reservation_code') }}" placeholder="Search code">
            </div>

            <div class="form-group">
                <label>Guest Name</label>
                <input type="text" name="guest_name" value="{{ request('guest_name') }}" placeholder="Search guest">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Status</option>
                    <option value="pending" @selected(request('status')=='pending' )>Pending</option>
                    <option value="confirmed" @selected(request('status')=='confirmed' )>Confirmed</option>
                    <option value="checked_in" @selected(request('status')=='checked_in' )>Checked In</option>
                    <option value="checked_out" @selected(request('status')=='checked_out' )>Checked Out</option>
                    <option value="cancelled" @selected(request('status')=='cancelled' )>Cancelled</option>
                    <option value="no_show" @selected(request('status')=='no_show' )>No Show</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Check In Date</label>
                <input type="date" name="check_in_date" value="{{ request('check_in_date') }}">
            </div>

            <div class="form-group">
                <label>Check Out Date</label>
                <input type="date" name="check_out_date" value="{{ request('check_out_date') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Reset</a>

        @auth
        @if (auth()->user()->canManageFrontDesk())
        <a href="{{ route('reservations.create') }}" class="btn btn-success">+ Create Reservation</a>
        @endif
        @endauth
    </form>
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Guest</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Base Total</th>
                <th>Restaurant</th>
                <th>Grand Total</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $reservation)
            @php
            $remainingAmount = $reservation->grand_total - $reservation->paid_amount;
            @endphp

            <tr>
                <td>{{ $reservation->reservation_code }}</td>
                <td>{{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</td>
                <td>{{ $reservation->check_in_date->format('Y-m-d') }}</td>
                <td>{{ $reservation->check_out_date->format('Y-m-d') }}</td>
                <td>
                    <span class="badge badge-{{ $reservation->status }}">
                        {{ ucwords(str_replace('_', ' ', $reservation->status)) }}
                    </span>
                </td>
                <td>{{ number_format($reservation->total_amount, 2) }}</td>
                <td>{{ number_format($reservation->restaurant_total, 2) }}</td>
                <td>{{ number_format($reservation->grand_total, 2) }}</td>
                <td>{{ number_format($reservation->paid_amount, 2) }}</td>
                <td>{{ number_format($remainingAmount, 2) }}</td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-success">View</a>

                        @auth
                        @if (auth()->user()->canManageFrontDesk())
                        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning">Edit</a>
                        @endif

                        @if (auth()->user()->isAdmin())
                        <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" class="inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this reservation?')">
                                Delete
                            </button>
                        </form>
                        @endif
                        @endauth
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11">No reservations found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $reservations->links() }}
    </div>
</div>
@endsection