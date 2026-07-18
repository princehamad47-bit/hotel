@extends('layouts.app')

@section('content')
<h1 class="page-title">Room Types</h1>

<div class="card">
    @auth
    @if (auth()->user()->isAdmin())
    <a href="{{ route('room-types.create') }}" class="btn btn-primary">+ Add Room Type</a>
    @endif
    @endauth
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Base Price</th>
                <th>Capacity</th>
                <th>Bed Type</th>
                <th>Rooms Count</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roomTypes as $roomType)
            <tr>
                <td>{{ $roomType->name }}</td>
                <td>{{ $roomType->description ?? '-' }}</td>
                <td>{{ number_format($roomType->base_price, 2) }}</td>
                <td>{{ $roomType->capacity }}</td>
                <td>{{ $roomType->bed_type ?? '-' }}</td>
                <td>{{ $roomType->rooms_count }}</td>
                <td>
                    <a href="{{ route('room-types.show', $roomType) }}" class="btn btn-success">View</a>

                    @auth
                    @if (auth()->user()->isAdmin())
                    <a href="{{ route('room-types.edit', $roomType) }}" class="btn btn-warning">Edit</a>

                    <form action="{{ route('room-types.destroy', $roomType) }}" method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this room type?')">
                            Delete
                        </button>
                    </form>
                    @endif
                    @endauth
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No room types found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $roomTypes->links() }}
    </div>
</div>
@endsection