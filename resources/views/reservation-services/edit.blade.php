@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Reservation Service</h1>

<div class="card">
    @auth
    @if (auth()->user()->canManageHousekeeping())
    <form method="POST" action="{{ route('reservation-services.update', $reservationService->id) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>Reservation</label>
                <input type="text" value="{{ $reservation->reservation_code }} - {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}" disabled>
            </div>

            <div class="form-group">
                <label>Service</label>
                <select name="service_id" required>
                    <option value="">Select Service</option>
                    @foreach ($services as $service)
                    <option value="{{ $service->id }}" @selected(old('service_id', $reservationService->service_id) == $service->id)>
                        {{ $service->name }} ({{ number_format($service->price, 2) }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Room</label>
                <select name="room_id">
                    <option value="">Select Room</option>
                    @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected(old('room_id', $reservationService->room_id) == $room->id)>
                        Room {{ $room->room_number }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Assigned Staff</label>
                <select name="assigned_staff_id">
                    <option value="">Select Staff</option>
                    @forelse ($staff as $member)
                    <option value="{{ $member->id }}" @selected(old('assigned_staff_id', $reservationService->assigned_staff_id) == $member->id)>
                        {{ $member->full_name }} - {{ $member->designation }}
                    </option>
                    @empty
                    <option value="" disabled>No staff found</option>
                    @endforelse
                </select>
            </div>
        </div>

        <div class="grid-3">
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', $reservationService->quantity) }}" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="pending" @selected(old('status', $reservationService->status) == 'pending')>Pending</option>
                    <option value="in_progress" @selected(old('status', $reservationService->status) == 'in_progress')>In Progress</option>
                    <option value="completed" @selected(old('status', $reservationService->status) == 'completed')>Completed</option>
                    <option value="cancelled" @selected(old('status', $reservationService->status) == 'cancelled')>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label>Service Date</label>
                <input type="datetime-local" name="service_date"
                    value="{{ old('service_date', $reservationService->service_date ? $reservationService->service_date->format('Y-m-d\TH:i') : '') }}">
            </div>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes">{{ old('notes', $reservationService->notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Service</button>
        <a href="{{ route('reservation-services.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit reservation services.</p>
    <a href="{{ route('reservation-services.index') }}" class="btn btn-secondary">Back</a>
    @endif
    @endauth
</div>
@endsection