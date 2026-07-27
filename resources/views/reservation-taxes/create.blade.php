@extends('layouts.app')

@section('content')
<h1 class="page-title">Apply Tax</h1>

<div class="card details-list">
    <p><strong>Reservation Code:</strong> {{ $reservation->reservation_code }}</p>
    <p><strong>Guest:</strong> {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</p>
    <p><strong>Room Total:</strong> {{ number_format($reservation->room_total, 2) }}</p>
    <p><strong>Service Total:</strong> {{ number_format($reservation->service_total, 2) }}</p>
    <p><strong>Restaurant Total:</strong> {{ number_format($reservation->restaurant_total, 2) }}</p>
    <p><strong>Subtotal:</strong> {{ number_format($reservation->sub_total, 2) }}</p>
</div>

<div class="card">
    <form method="POST" action="{{ route('reservation-taxes.store', $reservation) }}">
        @csrf

        <div class="form-group">
            <label>Select Tax Option(s)</label>

            @forelse ($taxes as $tax)
            <div class="room-box">
                <label style="margin-bottom: 0;">
                    <input type="checkbox" name="taxes[]" value="{{ $tax->id }}"
                        {{ in_array($tax->id, old('taxes', [])) ? 'checked' : '' }}>
                    <strong>{{ $tax->name }}</strong> |
                    {{ ucfirst($tax->type) }} |
                    @if ($tax->type === 'percentage')
                    {{ number_format($tax->value, 2) }}%
                    @else
                    {{ number_format($tax->value, 2) }}
                    @endif
                </label>
            </div>
            @empty
            <p>No active taxes found.</p>
            @endforelse
        </div>

        <button type="submit" class="btn btn-success">Apply Tax</button>
        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection