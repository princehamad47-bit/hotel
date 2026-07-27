@extends('layouts.app')

@section('content')
<h1 class="page-title">Room Type Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Name:</strong> {{ $roomType->name }}</p>
            <p><strong>Base Price:</strong> {{ number_format($roomType->base_price, 2) }}</p>
            <p><strong>Capacity:</strong> {{ $roomType->capacity }}</p>
            <p><strong>Bed Type:</strong> {{ $roomType->bed_type ?? '-' }}</p>
        </div>
        <div>
            <p><strong>Description:</strong> {{ $roomType->description ?? '-' }}</p>
            <p><strong>Total Rooms:</strong> {{ $roomType->rooms->count() }}</p>
        </div>
    </div>
</div>

@can('module-access', ['rooms', 'read'])
<div class="card">
    <h3 class="section-title">Rooms</h3>

    @if ($roomType->rooms->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Floor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roomType->rooms as $room)
                <tr>
                    <td>
                        <a href="{{ route('rooms.show', $room) }}">
                            {{ $room->room_number }}
                        </a>
                    </td>
                    <td>{{ $room->floor_number ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $room->status }}">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No rooms found for this room type.</p>
    @endif
</div>
@endcan

<a href="{{ route('room-types.index') }}" class="btn btn-secondary">Back</a>

@can('module-access', ['room-types', 'update'])
<a href="{{ route('room-types.edit', $roomType) }}" class="btn btn-warning">Edit</a>
@endcan
@endsection