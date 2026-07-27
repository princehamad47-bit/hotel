@extends('layouts.app')

@section('content')
<h1 class="page-title">Menu Items</h1>

<div class="card">
    @can('module-access', ['menu-items', 'create'])
    <a href="{{ route('menu-items.create') }}" class="btn btn-primary">+ Add Menu Item</a>
    @endcan
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Available</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($menuItems as $menuItem)
            <tr>
                <td>{{ $menuItem->name }}</td>
                <td>{{ $menuItem->menuCategory->name }}</td>
                <td>{{ number_format($menuItem->price, 2) }}</td>
                <td>{{ $menuItem->is_available ? 'Yes' : 'No' }}</td>
                <td>
                    <div class="action-buttons">
                        @can('module-access', ['menu-items', 'read'])
                        <a href="{{ route('menu-items.show', $menuItem) }}" class="btn btn-success">View</a>
                        @endcan

                        @can('module-access', ['menu-items', 'update'])
                        <a href="{{ route('menu-items.edit', $menuItem) }}" class="btn btn-warning">Edit</a>
                        @endcan

                        @can('module-access', ['menu-items', 'delete'])
                        <form action="{{ route('menu-items.destroy', $menuItem) }}" method="POST" class="inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this menu item?')">
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">No menu items found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $menuItems->links() }}
    </div>
</div>
@endsection