@extends('layouts.app')

@section('content')
<h1 class="page-title">Reservation Services</h1>

<div class="card">
    @can('module-access', ['reservation-services', 'create'])
    <a href="{{ route('reservation-services.create') }}" class="btn btn-primary">+ Add Reservation Service</a>
    @endcan
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Reservation Code</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Service</th>
                <th>Assigned To</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Total Price</th>
                <th>Service Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservationServices as $reservationService)
            <tr>
                <td>{{ $reservationService->reservation->reservation_code }}</td>
                <td>{{ $reservationService->reservation->guest->first_name }} {{ $reservationService->reservation->guest->last_name }}</td>
                <td>{{ $reservationService->room?->room_number ?? '-' }}</td>
                <td>{{ $reservationService->service->name }}</td>
                <td>{{ $reservationService->assignedStaff?->full_name ?? '-' }}</td>
                <td>{{ $reservationService->quantity }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $reservationService->status)) }}</td>
                <td>{{ number_format($reservationService->total_price, 2) }}</td>
                <td>{{ $reservationService->service_date ? $reservationService->service_date->format('Y-m-d H:i') : '-' }}</td>
                <td>
                    <div class="action-buttons">
                        @can('module-access', ['reservation-services', 'read'])
                        <a href="{{ route('reservation-services.show', $reservationService->id) }}" class="btn btn-success">View</a>
                        @endcan

                        @can('module-access', ['reservation-services', 'update'])
                        <a href="{{ route('reservation-services.edit', $reservationService->id) }}" class="btn btn-warning">Edit</a>
                        @endcan

                        @can('module-access', ['reservation-services', 'delete'])
                        <form action="{{ route('reservation-services.destroy', $reservationService->id) }}" method="POST" class="inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this reservation service?')">
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10">No reservation services found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $reservationServices->links() }}
    </div>
</div>
@endsection