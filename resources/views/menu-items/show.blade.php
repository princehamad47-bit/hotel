@extends('layouts.app')

@section('content')
<h1 class="page-title">Menu Item Details</h1>

<div class="card details-list">
    <div class="grid-2">
        <div>
            <p><strong>Name:</strong> {{ $menuItem->name }}</p>
            <p><strong>Category:</strong> {{ $menuItem->menuCategory->name }}</p>
            <p><strong>Price:</strong> {{ number_format($menuItem->price, 2) }}</p>
        </div>
        <div>
            <p><strong>Available:</strong> {{ $menuItem->is_available ? 'Yes' : 'No' }}</p>
            <p><strong>Description:</strong> {{ $menuItem->description ?? '-' }}</p>
        </div>
    </div>
</div>

<a href="{{ route('menu-items.index') }}" class="btn btn-secondary">Back</a>
<a href="{{ route('menu-items.edit', $menuItem) }}" class="btn btn-warning">Edit</a>
@endsection