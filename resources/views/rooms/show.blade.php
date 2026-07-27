@extends('layouts.app')

@section('content')
<h1 class="page-title">Room Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Room Number:</strong> {{ $room->room_number }}</p>
            <p><strong>Room Type:</strong> {{ $room->roomType->name }}</p>
            <p><strong>Floor Number:</strong> {{ $room->floor_number ?? '-' }}</p>
        </div>
        <div>
            <p>
                <strong>Status:</strong>
                <span class="badge badge-{{ $room->status }}">
                    {{ ucfirst($room->status) }}
                </span>
            </p>
            <p><strong>Base Price:</strong> {{ number_format($room->roomType->base_price, 2) }}</p>
            <p><strong>Capacity:</strong> {{ $room->roomType->capacity }}</p>
        </div>
    </div>

    <p><strong>Notes:</strong> {{ $room->notes ?? '-' }}</p>
</div>

<div class="card">
    <h3 class="section-title">Room Actions</h3>

    @can('module-access', ['rooms', 'update'])
    @if ($room->status === 'cleaning')
    <form action="{{ route('rooms.mark-available', $room) }}" method="POST" class="inline-form">
        @csrf
        <button type="submit" class="btn btn-success">Mark Available</button>
    </form>
    @endif

    @if (in_array($room->status, ['available', 'occupied']))
    <form action="{{ route('rooms.mark-cleaning', $room) }}" method="POST" class="inline-form">
        @csrf
        <button type="submit" class="btn btn-warning">Mark Cleaning</button>
    </form>
    @endif

    @if (in_array($room->status, ['available', 'cleaning']))
    <form action="{{ route('rooms.mark-maintenance', $room) }}" method="POST" class="inline-form">
        @csrf
        <button type="submit" class="btn btn-danger">Mark Maintenance</button>
    </form>
    @endif

    @if ($room->status === 'maintenance')
    <form action="{{ route('rooms.mark-available', $room) }}" method="POST" class="inline-form">
        @csrf
        <button type="submit" class="btn btn-success">Reopen Room</button>
    </form>
    @endif
    @else
    <p class="muted">You do not have permission to update room status.</p>
    @endcan
</div>

@can('module-access', ['reservations', 'read'])
<div class="card">
    <h3 class="section-title">Reservation History</h3>

    @if ($room->reservationRooms->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reservation Code</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Room Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($room->reservationRooms as $reservationRoom)
                <tr>
                    <td>
                        <a href="{{ route('reservations.show', $reservationRoom->reservation) }}">
                            {{ $reservationRoom->reservation->reservation_code }}
                        </a>
                    </td>
                    <td>{{ $reservationRoom->reservation->check_in_date->format('Y-m-d') }}</td>
                    <td>{{ $reservationRoom->reservation->check_out_date->format('Y-m-d') }}</td>
                    <td>
                        <span class="badge badge-{{ $reservationRoom->reservation->status }}">
                            {{ ucwords(str_replace('_', ' ', $reservationRoom->reservation->status)) }}
                        </span>
                    </td>
                    <td>{{ number_format($reservationRoom->room_rate, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No reservation history found for this room.</p>
    @endif
</div>
@endcan

<a href="{{ route('rooms.index') }}" class="btn btn-secondary">Back</a>

@can('module-access', ['rooms', 'update'])
<a href="{{ route('rooms.edit', $room) }}" class="btn btn-warning">Edit</a>
@endcan
@endsection