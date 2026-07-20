@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Tax</h1>

<div class="card">
    @auth
    @if (auth()->user()->isAdmin())
    <form method="POST" action="{{ route('taxes.update', $tax) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>Tax Name</label>
                <input type="text" name="name" value="{{ old('name', $tax->name) }}" required>
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type" required>
                    <option value="percentage" @selected(old('type', $tax->type) == 'percentage')>Percentage</option>
                    <option value="fixed" @selected(old('type', $tax->type) == 'fixed')>Fixed</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Value</label>
                <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $tax->value) }}" required>
            </div>

            <div class="form-group" style="display:flex; align-items:end;">
                <label style="display:flex; gap:8px; align-items:center; margin-bottom:0;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tax->is_active) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description', $tax->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Tax</button>
        <a href="{{ route('taxes.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit taxes.</p>
    <a href="{{ route('taxes.index') }}" class="btn btn-secondary">Back</a>
    @endif
    @endauth
</div>
@endsection