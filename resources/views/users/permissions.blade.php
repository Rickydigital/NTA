@extends('components.main-layout')

@section('title', 'Permissions')
@section('page-title', 'Permissions')

@section('content')
<div class="container-fluid">

    <div class="card mb-3">
        <div class="card-body">
            <div>
                <h4 class="mb-1">Permissions</h4>
                <small class="text-muted">System permissions are seeded and read-only</small>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('permissions.index') }}">
                <div class="row">
                    <div class="col-md-11">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search permissions...">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-dark w-100">Go</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info">
        Permissions are seeded by the system and cannot be created, edited, or deleted from this page.
    </div>

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Permission Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>{{ $loop->iteration + ($permissions->firstItem() - 1) }}</td>
                                <td><strong>{{ $permission->name }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No permissions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $permissions->links() }}
        </div>
    </div>
</div>
@endsection