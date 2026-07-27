@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Service</h1>

<div class="card">
    @can('module-access', ['services', 'update'])
    <form method="POST" action="{{ route('services.update', $service) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name', $service->name) }}" required>
            </div>

            <div class="form-group">
                <label>Service Type</label>
                <select name="service_type" required>
                    <option value="housekeeping" @selected(old('service_type', $service->service_type) == 'housekeeping')>Housekeeping</option>
                    <option value="laundry" @selected(old('service_type', $service->service_type) == 'laundry')>Laundry</option>
                    <option value="food" @selected(old('service_type', $service->service_type) == 'food')>Food</option>
                    <option value="transport" @selected(old('service_type', $service->service_type) == 'transport')>Transport</option>
                    <option value="minibar" @selected(old('service_type', $service->service_type) == 'minibar')>Mini Bar</option>
                    <option value="other" @selected(old('service_type', $service->service_type) == 'other')>Other</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $service->price) }}" required>
            </div>

            <div class="form-group" style="display:flex; align-items:end;">
                <label style="display:flex; gap:8px; align-items:center; margin-bottom:0;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                    Active
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description">{{ old('description', $service->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Service</button>
        <a href="{{ route('services.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit services.</p>
    <a href="{{ route('services.index') }}" class="btn btn-secondary">Back</a>
    @endcan
</div>
@endsection
