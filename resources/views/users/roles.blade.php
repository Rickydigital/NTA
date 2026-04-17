@extends('components.main-layout')

@section('title', 'Roles')
@section('page-title', 'Roles')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Roles</h4>
                <small class="text-muted">Manage roles and assign permissions</small>
            </div>

            @can('role.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                + Add Role
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('roles.index') }}">
                <div class="row">
                    <div class="col-md-11">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search roles...">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-dark w-100">Go</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role</th>
                            <th>Permissions Count</th>
                            <th>Permissions</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration + ($roles->firstItem() - 1) }}</td>
                                <td><strong>{{ $role->name }}</strong></td>
                                <td>{{ $role->permissions->count() }}</td>
                                <td>
                                    @if($role->permissions->count())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($role->permissions->take(6) as $permission)
                                                <span class="badge bg-light text-dark border">{{ $permission->name }}</span>
                                            @endforeach

                                            @if($role->permissions->count() > 6)
                                                <span class="badge bg-secondary">+{{ $role->permissions->count() - 6 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No permissions</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('role.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editRoleModal{{ $role->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('role.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('roles.destroy', $role) }}"
                                                      onsubmit="return confirm('Delete this role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            @can('role.update')
                            <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('roles.update', $role) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Role</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Role Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                                                </div>

                                                <div>
                                                    <label class="form-label">Permissions</label>
                                                    <div class="row g-2">
                                                        @foreach($permissions as $permission)
                                                            <div class="col-md-4">
                                                                <div class="form-check border rounded p-2">
                                                                    <input class="form-check-input"
                                                                           type="checkbox"
                                                                           name="permissions[]"
                                                                           value="{{ $permission->name }}"
                                                                           id="edit_role_{{ $role->id }}_permission_{{ $permission->id }}"
                                                                           {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="edit_role_{{ $role->id }}_permission_{{ $permission->id }}">
                                                                        {{ $permission->name }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endcan

                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No roles found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $roles->links() }}
        </div>
    </div>
</div>

@can('role.create')
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Role</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div>
                        <label class="form-label">Permissions</label>
                        <div class="row g-2">
                            @foreach($permissions as $permission)
                                <div class="col-md-4">
                                    <div class="form-check border rounded p-2">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               id="create_permission_{{ $permission->id }}"
                                               {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="create_permission_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection