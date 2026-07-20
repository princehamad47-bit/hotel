@extends('layouts.app')

@section('content')
<h1 class="page-title">Menu Categories</h1>

<div class="card">
    @auth
    @if (auth()->user()->isAdmin())
    <a href="{{ route('menu-categories.create') }}" class="btn btn-primary">+ Add Menu Category</a>
    @endif
    @endauth
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Active</th>
                <th>Menu Items</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($menuCategories as $menuCategory)
            <tr>
                <td>{{ $menuCategory->name }}</td>
                <td>{{ $menuCategory->description ?? '-' }}</td>
                <td>{{ $menuCategory->is_active ? 'Yes' : 'No' }}</td>
                <td>{{ $menuCategory->menuItems()->count() }}</td>
                <td>
                    <a href="{{ route('menu-categories.show', $menuCategory) }}" class="btn btn-success">View</a>

                    @auth
                    @if (auth()->user()->isAdmin())
                    <a href="{{ route('menu-categories.edit', $menuCategory) }}" class="btn btn-warning">Edit</a>

                    <form action="{{ route('menu-categories.destroy', $menuCategory) }}" method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this menu category?')">
                            Delete
                        </button>
                    </form>
                    @endif
                    @endauth
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">No menu categories found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $menuCategories->links() }}
    </div>
</div>
@endsection