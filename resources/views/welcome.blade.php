@extends('layouts.app')

@section('content')
<h1 class="page-title">Hotel Dashboard</h1>

<div class="grid-3">
    <div class="card">
        <h3 class="section-title">Reservations</h3>
        <p>Manage bookings, assign rooms, and track customer stays.</p>
        <br>
        <a href="{{ route('reservations.index') }}" class="btn btn-primary">Open Reservations</a>
    </div>

    <div class="card">
        <h3 class="section-title">Guests</h3>
        <p>Store guest details and connect them with reservations.</p>
        <br>
        <a href="{{ route('guests.index') }}" class="btn btn-primary">Open Guests</a>
    </div>

    <div class="card">
        <h3 class="section-title">Room Types</h3>
        <p>Create categories like Standard, Deluxe, and Suite.</p>
        <br>
        <a href="{{ route('room-types.index') }}" class="btn btn-primary">Open Room Types</a>
    </div>

    <div class="card">
        <h3 class="section-title">Rooms</h3>
        <p>Create rooms, assign types, and manage availability status.</p>
        <br>
        <a href="{{ route('rooms.index') }}" class="btn btn-primary">Open Rooms</a>
    </div>
</div>
@endsection