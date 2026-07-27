@extends('layouts.app')

@section('content')
<h1 class="page-title">Restaurant Order Details</h1>

@php
$remainingAmount = $restaurantOrder->grand_total - $restaurantOrder->paid_amount;
$isFullyPaid = $remainingAmount <= 0;
    @endphp

    <div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Order Code:</strong> {{ $restaurantOrder->order_code }}</p>
            <p><strong>Order Type:</strong> {{ ucwords(str_replace('_', ' ', $restaurantOrder->order_type)) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($restaurantOrder->status) }}</p>
            <p><strong>Table Number:</strong> {{ $restaurantOrder->table_number ?? '-' }}</p>
        </div>

        <div>
            <p><strong>Reservation:</strong> {{ $restaurantOrder->reservation?->reservation_code ?? '-' }}</p>
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
            <p>
                <strong>Phone:</strong>
                {{ $restaurantOrder->customer_phone ?? ($restaurantOrder->guest?->phone ?? $restaurantOrder->reservation?->guest?->phone ?? '-') }}
            </p>
            <p><strong>Notes:</strong> {{ $restaurantOrder->notes ?? '-' }}</p>
        </div>
    </div>
    </div>

    <div class="card">
        <h3 class="section-title">Billing Status</h3>

        @if ($isFullyPaid)
        <p><span class="badge badge-available">Fully Paid</span></p>
        @else
        <p><span class="badge badge-cancelled">Balance Due</span></p>
        <p><strong>Outstanding Balance:</strong> {{ number_format($remainingAmount, 2) }}</p>

        @can('module-access', ['restaurant-orders', 'create'])
        <a href="{{ route('restaurant-payments.create', $restaurantOrder) }}" class="btn btn-primary">Add Payment</a>
        @endcan
        @endif
    </div>

    <div class="card">
        <h3 class="section-title">Order Items</h3>

        @if ($restaurantOrder->items->count())
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($restaurantOrder->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->menuItem?->menuCategory?->name ?? '-' }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->subtotal, 2) }}</td>
                        <td>{{ $item->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p>No order items found.</p>
        @endif
    </div>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; gap:12px; flex-wrap:wrap;">
            <h3 class="section-title" style="margin-bottom:0;">Taxes</h3>

            @can('module-access', ['restaurant-orders', 'create'])
            <a href="{{ route('restaurant-order-taxes.create', $restaurantOrder) }}" class="btn btn-primary">+ Apply Tax</a>
            @endcan
        </div>

        @if ($restaurantOrder->taxes->count())
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
                    @foreach ($restaurantOrder->taxes as $restaurantTax)
                    <tr>
                        <td>{{ $restaurantTax->tax_name }}</td>
                        <td>{{ ucfirst($restaurantTax->tax_type) }}</td>
                        <td>
                            @if ($restaurantTax->tax_type === 'percentage')
                            {{ number_format($restaurantTax->tax_value, 2) }}%
                            @else
                            {{ number_format($restaurantTax->tax_value, 2) }}
                            @endif
                        </td>
                        <td>{{ number_format($restaurantTax->tax_amount, 2) }}</td>
                        <td>
                            @can('module-access', ['restaurant-orders', 'delete'])
                            <form action="{{ route('restaurant-order-taxes.destroy', [$restaurantOrder, $restaurantTax->id]) }}" method="POST" class="inline-form">
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

    <div class="card details-list">
        <h3 class="section-title">Billing Summary</h3>
        <p><strong>Subtotal:</strong> {{ number_format($restaurantOrder->subtotal, 2) }}</p>
        <p><strong>Tax Total:</strong> {{ number_format($restaurantOrder->tax_total, 2) }}</p>
        <p><strong>Grand Total:</strong> {{ number_format($restaurantOrder->grand_total, 2) }}</p>
        <p><strong>Paid Amount:</strong> {{ number_format($restaurantOrder->paid_amount, 2) }}</p>
        <p><strong>Remaining Amount:</strong> {{ number_format($remainingAmount, 2) }}</p>
    </div>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; gap:12px; flex-wrap:wrap;">
            <h3 class="section-title" style="margin-bottom:0;">Payments</h3>

            @can('module-access', ['restaurant-orders', 'create'])
            <a href="{{ route('restaurant-payments.create', $restaurantOrder) }}" class="btn btn-primary">+ Add Payment</a>
            @endcan
        </div>

        @if ($restaurantOrder->payments->count())
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
                    @foreach ($restaurantOrder->payments as $payment)
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
        @endif
    </div>

    <a href="{{ route('restaurant-orders.index') }}" class="btn btn-secondary">Back</a>

    @can('module-access', ['restaurant-orders', 'update'])
    <a href="{{ route('restaurant-orders.edit', $restaurantOrder) }}" class="btn btn-warning">Edit</a>
    @endcan

    @can('module-access', ['restaurant-orders', 'read'])
    <a href="{{ route('restaurant-orders.invoice', $restaurantOrder) }}" class="btn btn-primary" target="_blank">Print Invoice</a>
    @endcan
    @endsection