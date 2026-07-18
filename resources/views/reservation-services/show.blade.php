@extends('layouts.app')

@section('content')
<h1 class="page-title">Reservation Service Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Reservation Code:</strong> {{ $reservationService->reservation->reservation_code }}</p>
            <p><strong>Guest:</strong> {{ $reservationService->reservation->guest->first_name }} {{ $reservationService->reservation->guest->last_name }}</p>
            <p><strong>Room:</strong> {{ $reservationService->room?->room_number ?? '-' }}</p>
            <p><strong>Service:</strong> {{ $reservationService->service->name }}</p>
        </div>

        <div>
            <p><strong>Assigned Staff:</strong> {{ $reservationService->assignedStaff?->full_name ?? '-' }}</p>
            <p><strong>Quantity:</strong> {{ $reservationService->quantity }}</p>
            <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $reservationService->status)) }}</p>
            <p><strong>Total Price:</strong> {{ number_format($reservationService->total_price, 2) }}</p>
        </div>
    </div>

    <p><strong>Service Date:</strong> {{ $reservationService->service_date ? $reservationService->service_date->format('Y-m-d H:i') : '-' }}</p>
    <p><strong>Notes:</strong> {{ $reservationService->notes ?? '-' }}</p>
</div>

<a href="{{ route('reservation-services.index') }}" class="btn btn-secondary">Back</a>

@auth
@if (auth()->user()->canManageHousekeeping())
<a href="{{ route('reservation-services.edit', $reservationService->id) }}" class="btn btn-warning">Edit</a>
@endif
@endauth
@endsection