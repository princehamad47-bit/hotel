@extends('layouts.app')

@section('content')
<h1 class="page-title">Tax Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Name:</strong> {{ $tax->name }}</p>
            <p><strong>Type:</strong> {{ ucfirst($tax->type) }}</p>
            <p>
                <strong>Value:</strong>
                @if ($tax->type === 'percentage')
                {{ number_format($tax->value, 2) }}%
                @else
                {{ number_format($tax->value, 2) }}
                @endif
            </p>
        </div>

        <div>
            <p><strong>Status:</strong> {{ $tax->is_active ? 'Active' : 'Inactive' }}</p>
            <p><strong>Description:</strong> {{ $tax->description ?? '-' }}</p>
            <p><strong>Used In Reservations:</strong> {{ $tax->reservationTaxes->count() }}</p>
        </div>
    </div>
</div>

@can('module-access', ['reservations', 'read'])
<div class="card">
    <h3 class="section-title">Recent Usage</h3>

    @if ($tax->reservationTaxes->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reservation Code</th>
                    <th>Tax Name</th>
                    <th>Tax Type</th>
                    <th>Tax Value</th>
                    <th>Tax Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tax->reservationTaxes as $reservationTax)
                <tr>
                    <td>
                        @if ($reservationTax->reservation)
                        <a href="{{ route('reservations.show', $reservationTax->reservation) }}">
                            {{ $reservationTax->reservation->reservation_code }}
                        </a>
                        @else
                        -
                        @endif
                    </td>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No reservation usage found for this tax.</p>
    @endif
</div>
@endcan

<a href="{{ route('taxes.index') }}" class="btn btn-secondary">Back</a>

@can('module-access', ['taxes', 'update'])
<a href="{{ route('taxes.edit', $tax) }}" class="btn btn-warning">Edit</a>
@endcan
@endsection