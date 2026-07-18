@extends('layouts.app')

@section('content')
<h1 class="page-title">Add Room</h1>

<div class="card">
    @auth
    @if (auth()->user()->canManageHousekeeping())
    <form method="POST" action="{{ route('rooms.store') }}">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label>Room Number</label>
                <input type="text" name="room_number" value="{{ old('room_number') }}" required>
            </div>

            <div class="form-group">
                <label>Room Type</label>
                <select name="room_type_id" required>
                    <option value="">Select Room Type</option>
                    @foreach ($roomTypes as $roomType)
                    <option value="{{ $roomType->id }}" @selected(old('room_type_id')==$roomType->id)>
                        {{ $roomType->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Floor Number</label>
                <input type="number" name="floor_number" min="0" value="{{ old('floor_number') }}">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="available" @selected(old('status')=='available' )>Available</option>
                    <option value="occupied" @selected(old('status')=='occupied' )>Occupied</option>
                    <option value="maintenance" @selected(old('status')=='maintenance' )>Maintenance</option>
                    <option value="cleaning" @selected(old('status')=='cleaning' )>Cleaning</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Save Room</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to create rooms.</p>
    <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Back</a>
    @endif
    @endauth
</div>
@endsection