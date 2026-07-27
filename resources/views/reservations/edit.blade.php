@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Reservation</h1>

<div class="card">
    @can('module-access', ['reservations', 'update'])
    <form method="POST" action="{{ route('reservations.update', $reservation) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>Guest</label>
                <select name="guest_id" required>
                    <option value="">Select Guest</option>
                    @foreach ($guests as $guest)
                    <option value="{{ $guest->id }}" @selected(old('guest_id', $reservation->guest_id) == $guest->id)>
                        {{ $guest->first_name }} {{ $guest->last_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="pending" @selected(old('status', $reservation->status) == 'pending')>Pending</option>
                    <option value="confirmed" @selected(old('status', $reservation->status) == 'confirmed')>Confirmed</option>
                    <option value="cancelled" @selected(old('status', $reservation->status) == 'cancelled')>Cancelled</option>
                    <option value="no_show" @selected(old('status', $reservation->status) == 'no_show')>No Show</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Check In Date</label>
                <input type="date" name="check_in_date" value="{{ old('check_in_date', $reservation->check_in_date->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label>Check Out Date</label>
                <input type="date" name="check_out_date" value="{{ old('check_out_date', $reservation->check_out_date->format('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="grid-3">
            <div class="form-group">
                <label>Adults</label>
                <input type="number" name="adults" min="1" value="{{ old('adults', $reservation->adults) }}" required>
            </div>

            <div class="form-group">
                <label>Children</label>
                <input type="number" name="children" min="0" value="{{ old('children', $reservation->children) }}">
            </div>

            <div class="form-group">
                <label>Booking Source</label>
                <input type="text" name="booking_source" value="{{ old('booking_source', $reservation->booking_source) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes">{{ old('notes', $reservation->notes) }}</textarea>
        </div>

        <div class="form-group">
            <label>Rooms</label>
            @php
            $selectedRooms = old('rooms', $reservation->reservationRooms->pluck('room_id')->toArray());
            @endphp

            @foreach ($availableRooms as $room)
            <div class="room-box">
                <label style="margin-bottom: 0;">
                    <input type="checkbox" name="rooms[]" value="{{ $room->id }}"
                        {{ in_array($room->id, $selectedRooms) ? 'checked' : '' }}>
                    <strong>Room {{ $room->room_number }}</strong> |
                    {{ $room->roomType->name }} |
                    Price: {{ number_format($room->roomType->base_price, 2) }}
                </label>
            </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Update Reservation</button>
        <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit reservations.</p>
    <a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back</a>
    @endcan
</div>
@endsection
