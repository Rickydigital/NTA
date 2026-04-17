@extends('components.main-layout')

@section('title', 'Users')
@section('page-title', 'Users')

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
                <h4 class="mb-1">Users</h4>
                <small class="text-muted">Manage system users and assign roles</small>
            </div>

            @can('user.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                + Add User
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}">
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search by name, username, email or phone">
                    </div>

                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
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
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->firstItem() - 1) }}</td>
                                <td>{{ $user->full_name }}</td>
                                <td><strong>{{ $user->username }}</strong></td>
                                <td>{{ $user->gender ? ucfirst($user->gender) : '-' }}</td>
                                <td>{{ $user->phone_no ?: '-' }}</td>
                                <td>{{ $user->email ?: '-' }}</td>
                                <td>{{ $user->roles->first()?->name ?? '-' }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('user.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editUserModal{{ $user->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('user.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('users.destroy', $user) }}"
                                                      onsubmit="return confirm('Delete this user?')">
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

                            @can('user.update')
                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('users.update', $user) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit User</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Second Name</label>
                                                        <input type="text" name="second_name" class="form-control" value="{{ $user->second_name }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Gender</label>
                                                        <select name="gender" class="form-select">
                                                            <option value="">Select Gender</option>
                                                            <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Male</option>
                                                            <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Female</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Phone No</label>
                                                        <input type="text" name="phone_no" class="form-control" value="{{ $user->phone_no }}">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Username</label>
                                                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Role</label>
                                                        <select name="role" class="form-select" required>
                                                            <option value="">Select Role</option>
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->name }}" {{ $user->roles->first()?->name === $role->name ? 'selected' : '' }}>
                                                                    {{ $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Password</label>
                                                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Confirm Password</label>
                                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                                                    </div>

                                                    <div class="col-md-4 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   name="is_active"
                                                                   value="1"
                                                                   id="is_active_user_{{ $user->id }}"
                                                                   {{ $user->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_active_user_{{ $user->id }}">
                                                                Active User
                                                            </label>
                                                        </div>
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
                                <td colspan="9" class="text-center text-muted py-4">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
</div>

@can('user.create')
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add User</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Second Name</label>
                            <input type="text" name="second_name" class="form-control" value="{{ old('second_name') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Phone No</label>
                            <input type="text" name="phone_no" class="form-control" value="{{ old('phone_no') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_active"
                                       value="1"
                                       id="is_active_create_user"
                                       checked>
                                <label class="form-check-label" for="is_active_create_user">
                                    Active User
                                </label>
                            </div>
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