@extends('layouts.app')

@section('content')
<h1 class="page-title">Add Payment</h1>

@php
$remainingAmount = max(0, $reservation->total_amount - $reservation->paid_amount);
$isOverpaid = $reservation->paid_amount > $reservation->total_amount;
@endphp

<div class="card details-list">
    <p><strong>Reservation Code:</strong> {{ $reservation->reservation_code }}</p>
    <p><strong>Guest:</strong> {{ $reservation->guest->first_name }} {{ $reservation->guest->last_name }}</p>
    <p><strong>Total Amount:</strong> {{ number_format($reservation->total_amount, 2) }}</p>
    <p><strong>Paid Amount:</strong> {{ number_format($reservation->paid_amount, 2) }}</p>
    <p><strong>Remaining Amount:</strong> {{ number_format($remainingAmount, 2) }}</p>

    @if ($isOverpaid)
    <p class="text-danger">
        <strong>Overpaid Amount:</strong>
        {{ number_format($reservation->paid_amount - $reservation->total_amount, 2) }}
    </p>
    @endif
</div>

<div class="card">
    @if ($remainingAmount > 0)
    <form method="POST" action="{{ route('payments.store', $reservation) }}">
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
        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="text-danger">
        This reservation is already fully paid{{ $isOverpaid ? ' or overpaid' : '' }}. You cannot add another payment.
    </p>

    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">Back</a>
    @endif
</div>
@endsection