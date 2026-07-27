@extends('layouts.app')

@section('content')
<h1 class="page-title">Taxes</h1>

<div class="card">
    @can('module-access', ['taxes', 'create'])
    <a href="{{ route('taxes.create') }}" class="btn btn-primary">+ Add Tax</a>
    @endcan
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Value</th>
                <th>Status</th>
                <th>Used In Reservations</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($taxes as $tax)
            <tr>
                <td>{{ $tax->name }}</td>
                <td>{{ ucfirst($tax->type) }}</td>
                <td>
                    @if ($tax->type === 'percentage')
                    {{ number_format($tax->value, 2) }}%
                    @else
                    {{ number_format($tax->value, 2) }}
                    @endif
                </td>
                <td>
                    <span class="badge {{ $tax->is_active ? 'badge-available' : 'badge-cancelled' }}">
                        {{ $tax->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $tax->reservationTaxes()->count() }}</td>
                <td>
                    <div class="action-buttons">
                        @can('module-access', ['taxes', 'read'])
                        <a href="{{ route('taxes.show', $tax) }}" class="btn btn-success">View</a>
                        @endcan

                        @can('module-access', ['taxes', 'update'])
                        <a href="{{ route('taxes.edit', $tax) }}" class="btn btn-warning">Edit</a>
                        @endcan

                        @can('module-access', ['taxes', 'delete'])
                        <form action="{{ route('taxes.destroy', $tax) }}" method="POST" class="inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this tax?')">
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No taxes found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $taxes->links() }}
    </div>
</div>
@endsection