@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Menu Item</h1>

<div class="card">
    @can('module-access', ['menu-items', 'update'])
    <form method="POST" action="{{ route('menu-items.update', $menuItem) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>Category</label>
                <select name="menu_category_id" required>
                    @foreach ($menuCategories as $menuCategory)
                    <option value="{{ $menuCategory->id }}" @selected(old('menu_category_id', $menuItem->menu_category_id) == $menuCategory->id)>
                        {{ $menuCategory->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name', $menuItem->name) }}" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $menuItem->price) }}" required>
            </div>

            <div class="form-group" style="display:flex; align-items:end;">
                <label style="display:flex; gap:8px; align-items:center; margin-bottom:0;">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menuItem->is_available) ? 'checked' : '' }}>
                    Available
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description', $menuItem->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Menu Item</button>
        <a href="{{ route('menu-items.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit menu items.</p>

    @can('module-access', ['menu-items', 'read'])
    <a href="{{ route('menu-items.index') }}" class="btn btn-secondary">Back</a>
    @endcan
    @endcan
</div>
@endsection