@extends('layouts.app')

@section('content')
<h1 class="page-title">Restaurant Orders</h1>

<div class="card">
    @can('module-access', ['restaurant-orders', 'create'])
    <a href="{{ route('restaurant-orders.create') }}" class="btn btn-primary">+ Add Restaurant Order</a>
    @endcan
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Order Code</th>
                <th>Type</th>
                <th>Reservation</th>
                <th>Customer</th>
                <th>Table</th>
                <th>Status</th>
                <th>Grand Total</th>
                <th>Paid</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($restaurantOrders as $restaurantOrder)
            <tr>
                <td>{{ $restaurantOrder->order_code }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $restaurantOrder->order_type)) }}</td>
                <td>{{ $restaurantOrder->reservation?->reservation_code ?? '-' }}</td>
                <td>
                    @if ($restaurantOrder->guest)
                    {{ $restaurantOrder->guest->first_name }} {{ $restaurantOrder->guest->last_name }}
                    @else
                    {{ $restaurantOrder->customer_name ?? '-' }}
                    @endif
                </td>
                <td>{{ $restaurantOrder->table_number ?? '-' }}</td>
                <td>{{ ucfirst($restaurantOrder->status) }}</td>
                <td>{{ number_format($restaurantOrder->grand_total, 2) }}</td>
                <td>{{ number_format($restaurantOrder->paid_amount, 2) }}</td>
                <td>
                    <div class="action-buttons">
                        @can('module-access', ['restaurant-orders', 'read'])
                        <a href="{{ route('restaurant-orders.show', $restaurantOrder) }}" class="btn btn-success">View</a>
                        @endcan

                        @can('module-access', ['restaurant-orders', 'update'])
                        <a href="{{ route('restaurant-orders.edit', $restaurantOrder) }}" class="btn btn-warning">Edit</a>
                        @endcan

                        @can('module-access', ['restaurant-orders', 'delete'])
                        <form action="{{ route('restaurant-orders.destroy', $restaurantOrder) }}" method="POST" class="inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this restaurant order?')">
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">No restaurant orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $restaurantOrders->links() }}
    </div>
</div>
@endsection