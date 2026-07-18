@extends('layouts.app')

@section('content')
<h1 class="page-title">Guest Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>First Name:</strong> {{ $guest->first_name }}</p>
            <p><strong>Last Name:</strong> {{ $guest->last_name }}</p>
            <p><strong>Phone:</strong> {{ $guest->phone ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $guest->email ?? '-' }}</p>
        </div>

        <div>
            <p><strong>ID Type:</strong> {{ $guest->id_type ?? '-' }}</p>
            <p><strong>ID Number:</strong> {{ $guest->id_number ?? '-' }}</p>
            <p><strong>Nationality:</strong> {{ $guest->nationality ?? '-' }}</p>
            <p><strong>Address:</strong> {{ $guest->address ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="card">
    <h3 class="section-title">Reservations</h3>

    @if ($guest->reservations->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reservation Code</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guest->reservations as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_code }}</td>
                    <td>{{ $reservation->check_in_date }}</td>
                    <td>{{ $reservation->check_out_date }}</td>
                    <td>{{ $reservation->status }}</td>
                    <td>{{ number_format($reservation->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No reservations found for this guest.</p>
    @endif
</div>

<a href="{{ route('guests.index') }}" class="btn btn-secondary">Back</a>

@auth
@if (auth()->user()->canManageFrontDesk())
<a href="{{ route('guests.edit', $guest) }}" class="btn btn-warning">Edit</a>
@endif
@endauth
@endsection