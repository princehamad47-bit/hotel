@extends('layouts.app')

@section('content')
<h1 class="page-title">Reservation Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Reservation Code:</strong> {{ $reservation->reservation_code }}</p>
            <p><strong>Guest:</strong> {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</p>
            <p><strong>Check In:</strong> {{ $reservation->check_in_date->format('Y-m-d') }}</p>
            <p><strong>Check Out:</strong> {{ $reservation->check_out_date->format('Y-m-d') }}</p>
        </div>
        <div>
            <p>
                <strong>Status:</strong>
                <span class="badge badge-{{ $reservation->status }}">
                    {{ str_replace('_', ' ', ucfirst($reservation->status)) }}
                </span>
            </p>
            <p><strong>Total Amount:</strong> {{ number_format($reservation->total_amount, 2) }}</p>
            <p><strong>Paid Amount:</strong> {{ number_format($reservation->paid_amount, 2) }}</p>
            <p><strong>Remaining Amount:</strong> {{ number_format($reservation->total_amount - $reservation->paid_amount, 2) }}</p>
            <p><strong>Booking Source:</strong> {{ $reservation->booking_source ?? '-' }}</p>
            <p><strong>Checked In At:</strong> {{ $reservation->checked_in_at ? $reservation->checked_in_at->format('Y-m-d H:i') : '-' }}</p>
            <p><strong>Checked Out At:</strong> {{ $reservation->checked_out_at ? $reservation->checked_out_at->format('Y-m-d H:i') : '-' }}</p>
        </div>
    </div>
</div>

<div class="card">
    <h3 class="section-title">Reservation Actions</h3>

    @auth
    @if (auth()->user()->canManageFrontDesk())
    @if ($reservation->status === 'confirmed')
    <form action="{{ route('reservations.check-in', $reservation) }}" method="POST" class="inline-form">
        @csrf
        <button type="submit" class="btn btn-success">Check In</button>
    </form>
    @endif

    @if ($reservation->status === 'checked_in')
    <form action="{{ route('reservations.check-out', $reservation) }}" method="POST" class="inline-form">
        @csrf
        <button type="submit" class="btn btn-warning">Check Out</button>
    </form>
    @endif

    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-secondary">Edit Reservation</a>
    <a href="{{ route('reservations.invoice', $reservation) }}" class="btn btn-primary" target="_blank">Print Invoice</a>
    @endif
    @endauth
</div>

<div class="card">
    <h3 class="section-title">Rooms</h3>
    <ul class="simple-list">
        @foreach ($reservation->reservationRooms as $reservationRoom)
        <li>
            Room {{ $reservationRoom->room->room_number }}
            ({{ $reservationRoom->room->roomType->name }}) -
            Rate: {{ number_format($reservationRoom->room_rate, 2) }} -
            Nights: {{ $reservationRoom->nights }} -
            Subtotal: {{ number_format($reservationRoom->subtotal, 2) }}
        </li>
        @endforeach
    </ul>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; gap:12px; flex-wrap:wrap;">
        <h3 class="section-title" style="margin-bottom:0;">Payments</h3>

        @auth
        @if (auth()->user()->canManageFrontDesk())
        <a href="{{ route('payments.create', $reservation) }}" class="btn btn-primary">+ Add Payment</a>
        @endif
        @endauth
    </div>

    @if ($reservation->payments->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Reference</th>
                    <th>Paid At</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservation->payments as $payment)
                <tr>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    <td>{{ ucfirst($payment->payment_status) }}</td>
                    <td>{{ $payment->transaction_ref ?? '-' }}</td>
                    <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $payment->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No payments yet.</p>

    @auth
    @if (auth()->user()->canManageFrontDesk())
    <a href="{{ route('payments.create', $reservation) }}" class="btn btn-primary">Add First Payment</a>
    @endif
    @endauth
    @endif
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; gap:12px; flex-wrap:wrap;">
        <h3 class="section-title" style="margin-bottom:0;">Services</h3>

        @auth
        @if (auth()->user()->canManageHousekeeping())
        <a href="{{ route('reservation-services.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-primary">+ Add Service</a>
        @endif
        @endauth
    </div>

    @if ($reservation->reservationServices->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Room</th>
                    <th>Assigned Staff</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Total Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservation->reservationServices as $reservationService)
                <tr>
                    <td>{{ $reservationService->service->name }}</td>
                    <td>{{ $reservationService->room?->room_number ?? '-' }}</td>
                    <td>{{ $reservationService->assignedStaff?->full_name ?? '-' }}</td>
                    <td>{{ $reservationService->quantity }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $reservationService->status)) }}</td>
                    <td>{{ number_format($reservationService->total_price, 2) }}</td>
                    <td>{{ $reservationService->service_date ? $reservationService->service_date->format('Y-m-d H:i') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No services added yet.</p>
    @endif
</div>

<a href="{{ route('reservations.index') }}" class="btn btn-secondary">Back</a>

@auth
@if (auth()->user()->canManageFrontDesk())
<a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning">Edit</a>
@endif
@endauth
@endsection