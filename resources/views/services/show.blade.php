@extends('layouts.app')

@section('content')
<h1 class="page-title">Service Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Name:</strong> {{ $service->name }}</p>
            <p><strong>Type:</strong> {{ ucfirst($service->service_type) }}</p>
            <p><strong>Price:</strong> {{ number_format($service->price, 2) }}</p>
            <p><strong>Active:</strong> {{ $service->is_active ? 'Yes' : 'No' }}</p>
        </div>
        <div>
            <p><strong>Description:</strong> {{ $service->description ?? '-' }}</p>
            <p><strong>Used In Reservations:</strong> {{ $service->reservationServices->count() }}</p>
        </div>
    </div>
</div>

@can('module-access', ['reservation-services', 'read'])
<div class="card">
    <h3 class="section-title">Recent Usage</h3>

    @if ($service->reservationServices->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reservation Code</th>
                    <th>Guest</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($service->reservationServices as $item)
                <tr>
                    <td>{{ $item->reservation->reservation_code }}</td>
                    <td>{{ $item->reservation->guest->first_name }} {{ $item->reservation->guest->last_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No usage found for this service.</p>
    @endif
</div>
@endcan

<a href="{{ route('services.index') }}" class="btn btn-secondary">Back</a>

@can('module-access', ['services', 'update'])
<a href="{{ route('services.edit', $service) }}" class="btn btn-warning">Edit</a>
@endcan
@endsection