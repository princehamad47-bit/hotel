@extends('layouts.app')

@section('content')
<h1 class="page-title">Services</h1>

<div class="card">
    @can('module-access', ['services', 'create'])
    <a href="{{ route('services.create') }}" class="btn btn-primary">+ Add Service</a>
    @endcan
</div>

<div class="card table-responsive">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Price</th>
                <th>Active</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>{{ ucfirst($service->service_type) }}</td>
                <td>{{ number_format($service->price, 2) }}</td>
                <td>{{ $service->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <div class="action-buttons">
                        @can('module-access', ['services', 'read'])
                        <a href="{{ route('services.show', $service) }}" class="btn btn-success">View</a>
                        @endcan

                        @can('module-access', ['services', 'update'])
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-warning">Edit</a>
                        @endcan

                        @can('module-access', ['services', 'delete'])
                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this service?')">
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">No services found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $services->links() }}
    </div>
</div>
@endsection