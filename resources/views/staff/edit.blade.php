@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Staff</h1>

<div class="card">
    @auth
    @if (auth()->user()->isAdmin())
    <form method="POST" action="{{ route('staff.update', $staff) }}">
        @csrf
        @method('PUT')

        <div class="grid-2">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $staff->first_name) }}" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $staff->last_name) }}" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $staff->email) }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>CNIC / ID</label>
                <input type="text" name="cnic" value="{{ old('cnic', $staff->cnic) }}">
            </div>

            <div class="form-group">
                <label>Designation</label>
                <input type="text" name="designation" value="{{ old('designation', $staff->designation) }}" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" value="{{ old('department', $staff->department) }}">
            </div>

            <div class="form-group">
                <label>Salary</label>
                <input type="number" step="0.01" min="0" name="salary" value="{{ old('salary', $staff->salary) }}">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Join Date</label>
                <input type="date" name="join_date" value="{{ old('join_date', $staff->join_date ? $staff->join_date->format('Y-m-d') : '') }}">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="active" @selected(old('status', $staff->status) == 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $staff->status) == 'inactive')>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address">{{ old('address', $staff->address) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Staff</button>
        <a href="{{ route('staff.index') }}" class="btn btn-secondary">Back</a>
    </form>
    @else
    <p class="muted">You do not have permission to edit staff.</p>
    <a href="{{ route('staff.index') }}" class="btn btn-secondary">Back</a>
    @endif
    @endauth
</div>
@endsection