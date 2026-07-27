@extends('layouts.app')

@section('content')
<h1 class="page-title">Staff Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Name:</strong> {{ $staff->full_name }}</p>
            <p><strong>Phone:</strong> {{ $staff->phone ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $staff->email ?? '-' }}</p>
            <p><strong>CNIC / ID:</strong> {{ $staff->cnic ?? '-' }}</p>
        </div>

        <div>
            <p><strong>Designation:</strong> {{ $staff->designation }}</p>
            <p><strong>Department:</strong> {{ $staff->department ?? '-' }}</p>
            <p><strong>Salary:</strong> {{ $staff->salary !== null ? number_format($staff->salary, 2) : '-' }}</p>
            <p><strong>Join Date:</strong> {{ $staff->join_date ? $staff->join_date->format('Y-m-d') : '-' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($staff->status) }}</p>
        </div>
    </div>

    <p><strong>Address:</strong> {{ $staff->address ?? '-' }}</p>
</div>

@can('module-access', ['reservation-services', 'read'])
<div class="card">
    <h3 class="section-title">Assigned Reservation Services</h3>

    @if ($staff->reservationServices->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reservation Code</th>
                    <th>Guest</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staff->reservationServices as $item)
                <tr>
                    <td>
                        @can('module-access', ['reservations', 'read'])
                        <a href="{{ route('reservations.show', $item->reservation) }}">
                            {{ $item->reservation->reservation_code }}
                        </a>
                        @else
                        {{ $item->reservation->reservation_code }}
                        @endcan
                    </td>
                    <td>{{ $item->reservation->guest->first_name }} {{ $item->reservation->guest->last_name }}</td>
                    <td>{{ $item->service->name ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No assigned services found.</p>
    @endif
</div>
@endcan

<a href="{{ route('staff.index') }}" class="btn btn-secondary">Back</a>

@can('module-access', ['staff', 'update'])
<a href="{{ route('staff.edit', $staff) }}" class="btn btn-warning">Edit</a>
@endcan
@endsection