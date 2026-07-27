@extends('layouts.app')

@section('content')
<h1 class="page-title">Room Availability Board</h1>

<p class="muted" style="margin-bottom: 20px;">
    View rooms by floor and current status. Available rooms can be booked directly by front-desk users.
</p>

@forelse ($roomsByFloor as $floor => $rooms)
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
        <h3 class="section-title" style="margin-bottom:0;">
            Floor {{ $floor ?? 'N/A' }}
        </h3>

        <span class="muted">
            Total Rooms: {{ $rooms->count() }}
        </span>
    </div>

    <div class="room-board-grid">
        @foreach ($rooms as $room)
        @php
        $activeReservationRoom = $room->reservationRooms
        ->first(function ($reservationRoom) {
        return in_array($reservationRoom->reservation->status, ['confirmed', 'checked_in']);
        });

        $activeReservation = $activeReservationRoom?->reservation;
        @endphp

        <div class="room-status-card room-{{ $room->status }}">
            <div style="display:flex; justify-content:space-between; align-items:start; gap:10px; margin-bottom:10px;">
                <div>
                    <h4 style="margin:0 0 6px 0;">Room {{ $room->room_number }}</h4>
                    <p class="muted" style="margin:0;">{{ $room->roomType->name }}</p>
                </div>

                <span class="badge badge-{{ $room->status }}">
                    {{ ucfirst($room->status) }}
                </span>
            </div>

            <div style="margin-bottom:12px;">
                <p style="margin-bottom:6px;"><strong>Price:</strong> {{ number_format($room->roomType->base_price, 2) }}</p>
                <p style="margin-bottom:6px;"><strong>Capacity:</strong> {{ $room->roomType->capacity }}</p>
                <p style="margin-bottom:6px;"><strong>Bed:</strong> {{ $room->roomType->bed_type ?? '-' }}</p>

                @if ($room->status === 'occupied' && $activeReservation)
                <hr style="margin:10px 0; border:none; border-top:1px solid #e5e7eb;">
                <p style="margin-bottom:6px;"><strong>Guest:</strong> {{ $activeReservation->guest->first_name }} {{ $activeReservation->guest->last_name }}</p>
                <p style="margin-bottom:6px;"><strong>Reservation:</strong> {{ $activeReservation->reservation_code }}</p>
                <p style="margin-bottom:0;"><strong>Check Out:</strong> {{ $activeReservation->check_out_date->format('Y-m-d') }}</p>
                @elseif ($room->status === 'cleaning')
                <hr style="margin:10px 0; border:none; border-top:1px solid #e5e7eb;">
                <p style="margin-bottom:0;"><strong>Notice:</strong> Room under cleaning</p>
                @elseif ($room->status === 'maintenance')
                <hr style="margin:10px 0; border:none; border-top:1px solid #e5e7eb;">
                <p style="margin-bottom:0;"><strong>Notice:</strong> Room under maintenance</p>
                @endif
            </div>

            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                @if ($room->status === 'available')
                @can('module-access', ['reservations', 'create'])
                <a href="{{ route('reservations.create', ['room_id' => $room->id]) }}" class="btn btn-success">
                    Book Now
                </a>
                @elsecan('module-access', ['rooms', 'read'])
                <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">
                    View
                </a>
                @endcan
                @elseif ($room->status === 'occupied' && $activeReservation)
                @can('module-access', ['reservations', 'read'])
                <a href="{{ route('reservations.show', $activeReservation) }}" class="btn btn-primary">
                    Open Reservation
                </a>
                @endcan

                @can('module-access', ['rooms', 'read'])
                <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">
                    Room Details
                </a>
                @endcan
                @else
                @can('module-access', ['rooms', 'read'])
                <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">
                    View
                </a>
                @endcan
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="card">
    <p>No rooms found.</p>
</div>
@endforelse
@endsection