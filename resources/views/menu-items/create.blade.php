@extends('layouts.app')

@section('content')
<h1 class="page-title">Add Menu Item</h1>

<div class="card">
    @can('module-access', ['menu-items', 'create'])
    <form method="POST" action="{{ route('menu-items.store') }}">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label>Category</label>
                <select name="menu_category_id" required>
                    <option value="">Select Category</option>
                    @foreach ($menuCategories as $menuCategory)
                    <option value="{{ $menuCategory->id }}" @selected(old('menu_category_id')==$menuCategory->id)>
                        {{ $menuCategory->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required>
            </div>

            <div class="form-group" style="display:flex; align-items:end;">
                <label style="display:flex; gap:8px; align-items:center; margin-bottom:0;">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', 1) ? 'checked' : '' }}>
                    Available
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Save Menu Item</button>
        <a href="{{ route('menu-items.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to create menu items.</p>

    @can('module-access', ['menu-items', 'read'])
    <a href="{{ route('menu-items.index') }}" class="btn btn-secondary">Back</a>
    @endcan
    @endcan
</div>
@endsection