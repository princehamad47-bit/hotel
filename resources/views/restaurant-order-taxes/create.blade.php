@extends('layouts.app')

@section('content')
<h1 class="page-title">Apply Restaurant Tax</h1>

<div class="card details-list">
    <p><strong>Order Code:</strong> {{ $restaurantOrder->order_code }}</p>
    <p><strong>Order Type:</strong> {{ ucwords(str_replace('_', ' ', $restaurantOrder->order_type)) }}</p>
    <p><strong>Subtotal:</strong> {{ number_format($restaurantOrder->subtotal, 2) }}</p>
</div>

<div class="card">
    <form method="POST" action="{{ route('restaurant-order-taxes.store', $restaurantOrder) }}">
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
        <a href="{{ route('restaurant-orders.show', $restaurantOrder) }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection