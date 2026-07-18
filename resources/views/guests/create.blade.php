@extends('layouts.app')

@section('content')
<h1 class="page-title">Add Guest</h1>

<div class="card">
    @auth
    @if (auth()->user()->canManageFrontDesk())
    <form method="POST" action="{{ route('guests.store') }}">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" value="{{ old('email') }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>ID Type</label>
                <input type="text" name="id_type" value="{{ old('id_type') }}" placeholder="Passport / CNIC / Driving License">
            </div>

            <div class="form-group">
                <label>ID Number</label>
                <input type="text" name="id_number" value="{{ old('id_number') }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Nationality</label>
                <input type="text" name="nationality" value="{{ old('nationality') }}">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="{{ old('address') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Save Guest</button>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to create guests.</p>
    <a href="{{ route('guests.index') }}" class="btn btn-secondary">Back</a>
    @endif
    @endauth
</div>
@endsection