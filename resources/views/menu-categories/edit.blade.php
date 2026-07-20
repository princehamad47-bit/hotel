@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Menu Category</h1>

<div class="card">
    <form method="POST" action="{{ route('menu-categories.update', $menuCategory) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name', $menuCategory->name) }}" required>
            </div>

            <div class="form-group" style="display:flex; align-items:end;">
                <label style="display:flex; gap:8px; align-items:center; margin-bottom:0;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $menuCategory->is_active) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description', $menuCategory->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Menu Category</button>
        <a href="{{ route('menu-categories.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection