@extends('layouts.app')

@section('content')
<h1 class="page-title">Rooms</h1>

<div class="card">
    @auth
    @if (auth()->user()->canManageHousekeeping())
    <a href="{{ route('rooms.create') }}" class="btn btn-primary">+ Add Room</a>
    @endif
    @endauth
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Floor</th>
                <th>Status</th>
                <th>Base Price</th>
                <th>Capacity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rooms as $room)
            <tr>
                <td>{{ $room->room_number }}</td>
                <td>{{ $room->roomType->name }}</td>
                <td>{{ $room->floor_number ?? '-' }}</td>
                <td>
                    <span class="badge badge-{{ $room->status }}">
                        {{ ucfirst($room->status) }}
                    </span>
                </td>
                <td>{{ number_format($room->roomType->base_price, 2) }}</td>
                <td>{{ $room->roomType->capacity }}</td>
                <td>
                    <a href="{{ route('rooms.show', $room) }}" class="btn btn-success">View</a>

                    @auth
                    @if (auth()->user()->isAdmin())
                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-warning">Edit</a>

                    <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this room?')">
                            Delete
                        </button>
                    </form>
                    @endif
                    @endauth
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No rooms found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $rooms->links() }}
    </div>
</div>
@endsection