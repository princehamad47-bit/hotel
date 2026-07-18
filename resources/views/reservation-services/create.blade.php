@extends('layouts.app')

@section('content')
<h1 class="page-title">Add Reservation Service</h1>

<div class="card">
    @auth
    @if (auth()->user()->canManageHousekeeping())
    <form method="POST" action="{{ route('reservation-services.store') }}">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label>Reservation</label>
                <select name="reservation_id" id="reservation_id" required>
                    <option value="">Select Reservation</option>
                    @foreach ($reservations as $item)
                    <option value="{{ $item->id }}" @selected(old('reservation_id', optional($reservation)->id) == $item->id)>
                        {{ $item->reservation_code }} - {{ $item->guest->first_name }} {{ $item->guest->last_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Service</label>
                <select name="service_id" required>
                    <option value="">Select Service</option>
                    @foreach ($services as $service)
                    <option value="{{ $service->id }}" @selected(old('service_id')==$service->id)>
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
                    <option value="{{ $room->id }}" @selected(old('room_id')==$room->id)>
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
                    <option value="{{ $member->id }}" @selected(old('assigned_staff_id')==$member->id)>
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
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="pending" @selected(old('status')=='pending' )>Pending</option>
                    <option value="in_progress" @selected(old('status')=='in_progress' )>In Progress</option>
                    <option value="completed" @selected(old('status')=='completed' )>Completed</option>
                    <option value="cancelled" @selected(old('status')=='cancelled' )>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label>Service Date</label>
                <input type="datetime-local" name="service_date" value="{{ old('service_date') }}">
            </div>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Save Service</button>
        <a href="{{ route('reservation-services.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to create reservation services.</p>
    <a href="{{ route('reservation-services.index') }}" class="btn btn-secondary">Back</a>
    @endif
    @endauth
</div>

@auth
@if (auth()->user()->canManageHousekeeping())
<script>
    document.getElementById('reservation_id').addEventListener('change', function() {
        if (this.value) {
            window.location.href = "{{ route('reservation-services.create') }}" + "?reservation_id=" + this.value;
        }
    });
</script>
@endif
@endauth
@endsection