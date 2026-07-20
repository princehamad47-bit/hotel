@extends('layouts.app')

@section('content')
<h1 class="page-title">Add Restaurant Payment</h1>

@php
$remainingAmount = max(0, $restaurantOrder->grand_total - $restaurantOrder->paid_amount);
$isOverpaid = $restaurantOrder->paid_amount > $restaurantOrder->grand_total;
@endphp

<div class="card details-list">
    <p><strong>Order Code:</strong> {{ $restaurantOrder->order_code }}</p>
    <p><strong>Order Type:</strong> {{ ucwords(str_replace('_', ' ', $restaurantOrder->order_type)) }}</p>
    <p>
        <strong>Customer:</strong>
        @if ($restaurantOrder->guest)
        {{ $restaurantOrder->guest->first_name }} {{ $restaurantOrder->guest->last_name }}
        @elseif ($restaurantOrder->reservation?->guest)
        {{ $restaurantOrder->reservation->guest->first_name }} {{ $restaurantOrder->reservation->guest->last_name }}
        @else
        {{ $restaurantOrder->customer_name ?? '-' }}
        @endif
    </p>
    <p><strong>Grand Total:</strong> {{ number_format($restaurantOrder->grand_total, 2) }}</p>
    <p><strong>Paid Amount:</strong> {{ number_format($restaurantOrder->paid_amount, 2) }}</p>
    <p><strong>Remaining Amount:</strong> {{ number_format($remainingAmount, 2) }}</p>

    @if ($isOverpaid)
    <p class="text-danger">
        <strong>Overpaid Amount:</strong>
        {{ number_format($restaurantOrder->paid_amount - $restaurantOrder->grand_total, 2) }}
    </p>
    @endif
</div>

<div class="card">
    @if ($remainingAmount > 0)
    <form method="POST" action="{{ route('restaurant-payments.store', $restaurantOrder) }}">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label>Amount</label>
                <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    max="{{ number_format($remainingAmount, 2, '.', '') }}"
                    name="amount"
                    value="{{ old('amount', number_format($remainingAmount, 2, '.', '')) }}"
                    required>
                <small class="muted">Default is remaining amount. You can change it.</small>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="">Select Method</option>
                    <option value="cash" @selected(old('payment_method')=='cash' )>Cash</option>
                    <option value="card" @selected(old('payment_method')=='card' )>Card</option>
                    <option value="bank_transfer" @selected(old('payment_method')=='bank_transfer' )>Bank Transfer</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Payment Status</label>
                <select name="payment_status" required>
                    <option value="paid" @selected(old('payment_status', 'paid' )=='paid' )>Paid</option>
                    <option value="pending" @selected(old('payment_status')=='pending' )>Pending</option>
                    <option value="refunded" @selected(old('payment_status')=='refunded' )>Refunded</option>
                </select>
            </div>

            <div class="form-group">
                <label>Transaction Reference</label>
                <input type="text" name="transaction_ref" value="{{ old('transaction_ref') }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Paid At</label>
                <input
                    type="datetime-local"
                    name="paid_at"
                    value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <input type="text" name="notes" value="{{ old('notes') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Save Payment</button>
        <a href="{{ route('restaurant-orders.show', $restaurantOrder) }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="text-danger">
        This restaurant order is already fully paid{{ $isOverpaid ? ' or overpaid' : '' }}. You cannot add another payment.
    </p>

    <a href="{{ route('restaurant-orders.show', $restaurantOrder) }}" class="btn btn-secondary">Back</a>
    @endif
</div>
@endsection