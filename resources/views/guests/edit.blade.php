@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Guest</h1>

<div class="card">
    @can('module-access', ['guests', 'update'])
    <form method="POST" action="{{ route('guests.update', $guest) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $guest->first_name) }}" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $guest->last_name) }}" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $guest->phone) }}">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" value="{{ old('email', $guest->email) }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>ID Type</label>
                <input type="text" name="id_type" value="{{ old('id_type', $guest->id_type) }}">
            </div>

            <div class="form-group">
                <label>ID Number</label>
                <input type="text" name="id_number" value="{{ old('id_number', $guest->id_number) }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Nationality</label>
                <input type="text" name="nationality" value="{{ old('nationality', $guest->nationality) }}">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="{{ old('address', $guest->address) }}">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update Guest</button>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit guests.</p>
    <a href="{{ route('guests.index') }}" class="btn btn-secondary">Back</a>
    @endcan
</div>
@endsection
