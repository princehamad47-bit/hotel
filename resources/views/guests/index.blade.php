@extends('layouts.app')

@section('content')
<h1 class="page-title">Guests</h1>

<div class="card">
    @auth
    @if (auth()->user()->canManageFrontDesk())
    <a href="{{ route('guests.create') }}" class="btn btn-primary">+ Add Guest</a>
    @endif
    @endauth
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>ID Type</th>
                <th>ID Number</th>
                <th>Nationality</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($guests as $guest)
            <tr>
                <td>{{ $guest->first_name }} {{ $guest->last_name }}</td>
                <td>{{ $guest->phone ?? '-' }}</td>
                <td>{{ $guest->email ?? '-' }}</td>
                <td>{{ $guest->id_type ?? '-' }}</td>
                <td>{{ $guest->id_number ?? '-' }}</td>
                <td>{{ $guest->nationality ?? '-' }}</td>
                <td>
                    <a href="{{ route('guests.show', $guest) }}" class="btn btn-success">View</a>

                    @auth
                    @if (auth()->user()->isAdmin())
                    <a href="{{ route('guests.edit', $guest) }}" class="btn btn-warning">Edit</a>
                    @endif

                    @if (auth()->user()->isAdmin())
                    <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this guest?')">
                            Delete
                        </button>
                    </form>
                    @endif
                    @endauth
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No guests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $guests->links() }}
    </div>
</div>
@endsection