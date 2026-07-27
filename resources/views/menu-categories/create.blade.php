@extends('layouts.app')

@section('content')
<h1 class="page-title">Add Menu Category</h1>

<div class="card">
    @can('module-access', ['menu-categories', 'create'])
    <form method="POST" action="{{ route('menu-categories.store') }}">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group" style="display:flex; align-items:end;">
                <label style="display:flex; gap:8px; align-items:center; margin-bottom:0;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Save Menu Category</button>
        <a href="{{ route('menu-categories.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to create menu categories.</p>

    @can('module-access', ['menu-categories', 'read'])
    <a href="{{ route('menu-categories.index') }}" class="btn btn-secondary">Back</a>
    @endcan
    @endcan
</div>
@endsection