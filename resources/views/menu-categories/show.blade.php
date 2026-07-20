@extends('layouts.app')

@section('content')
<h1 class="page-title">Menu Category Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Name:</strong> {{ $menuCategory->name }}</p>
            <p><strong>Active:</strong> {{ $menuCategory->is_active ? 'Yes' : 'No' }}</p>
        </div>
        <div>
            <p><strong>Description:</strong> {{ $menuCategory->description ?? '-' }}</p>
            <p><strong>Total Menu Items:</strong> {{ $menuCategory->menuItems->count() }}</p>
        </div>
    </div>
</div>

<div class="card">
    <h3 class="section-title">Menu Items</h3>

    @if ($menuCategory->menuItems->count())
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Available</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($menuCategory->menuItems as $menuItem)
                <tr>
                    <td>{{ $menuItem->name }}</td>
                    <td>{{ number_format($menuItem->price, 2) }}</td>
                    <td>{{ $menuItem->is_available ? 'Yes' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>No menu items found for this category.</p>
    @endif
</div>

<a href="{{ route('menu-categories.index') }}" class="btn btn-secondary">Back</a>
<a href="{{ route('menu-categories.edit', $menuCategory) }}" class="btn btn-warning">Edit</a>
@endsection