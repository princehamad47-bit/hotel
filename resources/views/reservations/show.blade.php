@extends('layouts.app')

@section('content')
<h1 class="page-title">Reservation Details</h1>

@php
$remainingAmount = $reservation->grand_total - $reservation->paid_amount;
$isFullyPaid = $remainingAmount <= 0;
    $paidPercentage=$reservation->grand_total > 0
    ? min(100, max(0, ($reservation->paid_amount / $reservation->grand_total) * 100))
    : 0;
    @endphp

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
                <p><strong>Room Total:</strong> {{ number_format($reservation->room_total, 2) }}</p>
                <p><strong>Service Total:</strong> {{ number_format($reservation->service_total, 2) }}</p>
                <p><strong>Restaurant Total:</strong> {{ number_format($reservation->restaurant_total, 2) }}</p>
                <p><strong>Subtotal:</strong> {{ number_format($reservation->sub_total, 2) }}</p>
                <p><strong>Tax Total:</strong> {{ number_format($reservation->tax_total, 2) }}</p>
                <p><strong>Grand Total:</strong> {{ number_format($reservation->grand_total, 2) }}</p>
                <p><strong>Paid Amount:</strong> {{ number_format($reservation->paid_amount, 2) }}</p>
                <p><strong>Remaining Amount:</strong> {{ number_format($remainingAmount, 2) }}</p>
                <p><strong>Booking Source:</strong> {{ $reservation->booking_source ?? '-' }}</p>
                <p><strong>Checked In At:</strong> {{ $reservation->checked_in_at ? $reservation->checked_in_at->format('Y-m-d H:i') : '-' }}</p>
                <p><strong>Checked Out At:</strong> {{ $reservation->checked_out_at ? $reservation->checked_out_at->format('Y-m-d H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="section-title">Billing Status</h3>

        <div class="payment-progress-wrap">
            <div class="payment-progress-head">
                <strong>Payment Progress</strong>
                <span class="muted">{{ number_format($paidPercentage, 0) }}% Paid</span>
            </div>

            <progress
                class="payment-progress-bar {{ $isFullyPaid ? 'paid-full' : 'paid-partial' }}"
                value="{{ $paidPercentage }}"
                max="100">
            </progress>
        </div>

        @if ($isFullyPaid)
        <p><span class="badge badge-available">Fully Paid</span></p>
        <p class="muted">This reservation is cleared for checkout.</p>
        @else
        <p><span class="badge badge-cancelled">Balance Due</span></p>
        <p><strong>Outstanding Balance:</strong> {{ number_format($remainingAmount, 2) }}</p>

        @can('module-access', ['reservations', 'create'])
        <a href="{{ route('payments.create', $reservation) }}" class="btn btn-primary">Add Payment</a>
        @endcan
        @endif
    </div>

    <div class="card">
        <h3 class="section-title">Reservation Actions</h3>

        @can('module-access', ['reservations', 'update'])
        @if ($reservation->status === 'confirmed')
        <form action="{{ route('reservations.check-in', $reservation) }}" method="POST" class="inline-form">
            @csrf
            <button type="submit" class="btn btn-success">Check In</button>
        </form>
        @endif

        @if ($reservation->status === 'checked_in')
        @if ($isFullyPaid)
        <form action="{{ route('reservations.check-out', $reservation) }}" method="POST" class="inline-form">
            @csrf
            <button type="submit" class="btn btn-warning">Check Out</button>
        </form>
        @else
        <button type="button" class="btn btn-warning" disabled style="opacity:.65; cursor:not-allowed;">
            Check Out
        </button>
        <p class="muted" style="margin-top:10px;">
            Checkout is disabled until the full payment is received.
        </p>
        @endif
        @endif

        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-secondary">Edit Reservation</a>
        @endcan

        @can('module-access', ['reservations', 'read'])
        <a href="{{ route('reservations.invoice', $reservation) }}" class="btn btn-primary" target="_blank">Print Invoice</a>
        @endcan
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
            <h3 class="section-title" style="margin-bottom:0;">Taxes</h3>

            @can('module-access', ['reservations', 'create'])
            <a href="{{ route('reservation-taxes.create', $reservation) }}" class="btn btn-primary">+ Apply Tax</a>
            @endcan
        </div>

        @if ($reservation->taxes->count())
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Tax Name</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Tax Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservation->taxes as $reservationTax)
                    <tr>
                        <td>{{ $reservationTax->tax_name }}</td>
                        <td>{{ ucfirst($reservationTax->tax_type) }}</td>
                        <td>
                            @if ($reservationTax->tax_type === 'percentage')
                            {{ number_format($reservationTax->tax_value, 2) }}%
                            @else
                            {{ number_format($reservationTax->tax_value, 2) }}
                            @endif
                        </td>
                        <td>{{ number_format($reservationTax->tax_amount, 2) }}</td>
                        <td>
                            @can('module-access', ['reservations', 'delete'])
                            <form action="{{ route('reservation-taxes.destroy', [$reservation, $reservationTax->id]) }}" method="POST" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Remove this tax?')">
                                    Remove
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p>No taxes applied yet.</p>
        @endif
    </div>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; gap:12px; flex-wrap:wrap;">
            <h3 class="section-title" style="margin-bottom:0;">Restaurant Orders</h3>

            @can('module-access', ['restaurant-orders', 'create'])
            <a href="{{ route('restaurant-orders.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-primary">+ Add Restaurant Order</a>
            @endcan
        </div>

        @if ($reservation->restaurantOrders->where('status', '!=', 'cancelled')->count())
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Table</th>
                        <th>Items</th>
                        <th>Grand Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservation->restaurantOrders->where('status', '!=', 'cancelled') as $restaurantOrder)
                    <tr>
                        <td>{{ $restaurantOrder->order_code }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $restaurantOrder->order_type)) }}</td>
                        <td>{{ ucfirst($restaurantOrder->status) }}</td>
                        <td>{{ $restaurantOrder->table_number ?? '-' }}</td>
                        <td>{{ $restaurantOrder->items->count() }}</td>
                        <td>{{ number_format($restaurantOrder->grand_total, 2) }}</td>
                        <td>
                            @can('module-access', ['restaurant-orders', 'read'])
                            <a href="{{ route('restaurant-orders.show', $restaurantOrder) }}" class="btn btn-success">View</a>
                            @endcan

                            @can('module-access', ['restaurant-orders', 'update'])
                            <a href="{{ route('restaurant-orders.edit', $restaurantOrder) }}" class="btn btn-warning">Edit</a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p>No restaurant orders added yet.</p>

        @can('module-access', ['restaurant-orders', 'create'])
        <a href="{{ route('restaurant-orders.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-primary">Add Restaurant Order</a>
        @endcan
        @endif
    </div>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; gap:12px; flex-wrap:wrap;">
            <h3 class="section-title" style="margin-bottom:0;">Payments</h3>

            @can('module-access', ['reservations', 'create'])
            <a href="{{ route('payments.create', $reservation) }}" class="btn btn-primary">+ Add Payment</a>
            @endcan
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

        @can('module-access', ['reservations', 'create'])
        <a href="{{ route('payments.create', $reservation) }}" class="btn btn-primary">Add First Payment</a>
        @endcan
        @endif
    </div>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; gap:12px; flex-wrap:wrap;">
            <h3 class="section-title" style="margin-bottom:0;">Services</h3>

            @can('module-access', ['reservation-services', 'create'])
            <a href="{{ route('reservation-services.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-primary">+ Add Service</a>
            @endcan
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

    @can('module-access', ['reservations', 'update'])
    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning">Edit</a>
    @endcan
    @endsection