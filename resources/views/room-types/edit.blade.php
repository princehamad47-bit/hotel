@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Room Type</h1>

<div class="card">
    @auth
    @if (auth()->user()->isAdmin())
    <form method="POST" action="{{ route('room-types.update', $roomType) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>Room Type Name</label>
                <select name="name" required>
                    <option value="">Select Room Type</option>
                    <option value="Standard" @selected(old('name', $roomType->name) == 'Standard')>Standard</option>
                    <option value="Deluxe" @selected(old('name', $roomType->name) == 'Deluxe')>Deluxe</option>
                    <option value="Suite" @selected(old('name', $roomType->name) == 'Suite')>Suite</option>
                    <option value="Family" @selected(old('name', $roomType->name) == 'Family')>Family</option>
                    <option value="Executive" @selected(old('name', $roomType->name) == 'Executive')>Executive</option>
                </select>
            </div>

            <div class="form-group">
                <label>Base Price</label>
                <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $roomType->base_price) }}" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Capacity</label>
                <input type="number" name="capacity" min="1" value="{{ old('capacity', $roomType->capacity) }}" required>
            </div>

            <div class="form-group">
                <label>Bed Type</label>
                <select name="bed_type">
                    <option value="">Select Bed Type</option>
                    <option value="Single" @selected(old('bed_type', $roomType->bed_type) == 'Single')>Single</option>
                    <option value="Double" @selected(old('bed_type', $roomType->bed_type) == 'Double')>Double</option>
                    <option value="Twin" @selected(old('bed_type', $roomType->bed_type) == 'Twin')>Twin</option>
                    <option value="Queen" @selected(old('bed_type', $roomType->bed_type) == 'Queen')>Queen</option>
                    <option value="King" @selected(old('bed_type', $roomType->bed_type) == 'King')>King</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description', $roomType->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Room Type</button>
        <a href="{{ route('room-types.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit room types.</p>
    <a href="{{ route('room-types.index') }}" class="btn btn-secondary">Back</a>
    @endif
    @endauth
</div>
@endsection