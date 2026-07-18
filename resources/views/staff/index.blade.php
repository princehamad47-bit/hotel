@extends('layouts.app')

@section('content')
<h1 class="page-title">Staff</h1>

<div class="card">
    @auth
    @if (auth()->user()->isAdmin())
    <a href="{{ route('staff.create') }}" class="btn btn-primary">+ Add Staff</a>
    @endif
    @endauth
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Join Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($staff as $member)
            <tr>
                <td>{{ $member->full_name }}</td>
                <td>{{ $member->designation }}</td>
                <td>{{ $member->department ?? '-' }}</td>
                <td>{{ $member->phone ?? '-' }}</td>
                <td>{{ $member->email ?? '-' }}</td>
                <td>{{ $member->join_date ? $member->join_date->format('Y-m-d') : '-' }}</td>
                <td>{{ ucfirst($member->status) }}</td>
                <td>
                    <a href="{{ route('staff.show', $member) }}" class="btn btn-success">View</a>

                    @auth
                    @if (auth()->user()->isAdmin())
                    <a href="{{ route('staff.edit', $member) }}" class="btn btn-warning">Edit</a>

                    <form action="{{ route('staff.destroy', $member) }}" method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this staff member?')">
                            Delete
                        </button>
                    </form>
                    @endif
                    @endauth
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">No staff found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $staff->links() }}
    </div>
</div>
@endsection