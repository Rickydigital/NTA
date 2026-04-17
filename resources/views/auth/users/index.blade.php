@extends('components.main-layout')
@section('title', __('messages.users', ['default' => 'Users']))

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">{{ __('messages.users_list_title', ['default' => 'Users List']) }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('messages.users', ['default' => 'Users']) }}</li>
            </ol>
        </nav>
    </div>
    @if(auth()->user()->hasRole(['Admin', 'Cluster Coordinator', 'Gender Desk Coordinator']))
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-plus mr-1"></i>{{ __('messages.new', ['default' => 'New']) }} {{ __('messages.users', ['default' => 'User']) }}
        </a>
    </div>
    @endif
</div>

{{-- Desktop Table --}}
<div class="table-card d-none d-md-block">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('messages.table_headers.full_name', ['default' => 'Full Name']) }}</th>
                <th>{{ __('messages.table_headers.email', ['default' => 'Email']) }}</th>
                <th>{{ __('messages.table_headers.gender', ['default' => 'Gender']) }}</th>
                <th>{{ __('messages.table_headers.institution_name', ['default' => 'Institution']) }}</th>
                <th>{{ __('messages.roles', ['default' => 'Role']) }}</th>
                <th>{{ __('messages.table_headers.status_label', ['default' => 'Status']) }}</th>
                <th>{{ __('messages.table_headers.actions', ['default' => 'Actions']) }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->gender ?: '—' }}</td>
                <td>{{ $user->institution ? $user->institution->name : '—' }}</td>
                <td>{{ $user->getRoleNames()->first() ?: __('messages.no_role', ['default' => 'No Role']) }}</td>
                <td>
                    <span class="s-badge {{ strtolower($user->status) === 'active' ? 's-active' : 's-inactive' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('users.show', $user) }}" class="btn-icon" title="{{ __('messages.view_user', ['default' => 'View User']) }}">
                        <i class="mdi mdi-eye-outline"></i>
                    </a>
                    @if(
                        auth()->user()->hasRole('Admin') ||
                        (
                            auth()->user()->hasRole('Cluster Coordinator') &&
                            in_array($user->getRoleNames()->first(), ['Gender Desk Coordinator', 'Gender Desk Member']) &&
                            optional($user->institution?->ward?->district?->region)->cluster_id === optional(auth()->user()->institution?->ward?->district?->region)->cluster_id
                        ) ||
                        (
                            auth()->user()->hasRole('Gender Desk Coordinator') &&
                            $user->getRoleNames()->first() == 'Gender Desk Member' &&
                            $user->institution_id == auth()->user()->institution_id
                        )
                    )
                    <a href="{{ route('users.edit', $user) }}" class="btn-icon" title="{{ __('messages.edit_user', ['default' => 'Edit User']) }}">
                        <i class="mdi mdi-account-edit-outline"></i>
                    </a>
                    @endif
                    @if(auth()->user()->hasRole('Admin'))
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('{{ __('messages.confirm_delete', ['default' => 'Are you sure you want to delete this user?']) }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon danger" title="{{ __('messages.delete_user', ['default' => 'Delete User']) }}">
                            <i class="mdi mdi-trash-can-outline"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="tbl-empty">
                        <i class="mdi mdi-account-off-outline"></i>
                        <p>{{ __('messages.no_users_found', ['default' => 'No users found']) }}</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if(method_exists($users, 'hasPages') && $users->hasPages())
    <div class="px-3 py-2 border-top">
        {{ $users->links('vendor.pagination.bootstrap-4') }}
    </div>
    @endif
</div>

{{-- Mobile Cards --}}
<div class="d-md-none">
    @forelse ($users as $user)
    <div class="mob-card">
        <div class="mob-card-top">
            <span class="fw-600">{{ $user->name }}</span>
            <span class="s-badge {{ strtolower($user->status) === 'active' ? 's-active' : 's-inactive' }}">
                {{ ucfirst($user->status) }}
            </span>
        </div>
        <div class="mob-card-body">
            <p class="mob-card-title">{{ $user->getRoleNames()->first() ?: __('messages.no_role', ['default' => 'No Role']) }}</p>
        </div>
        <div class="mob-card-meta">
            <span><i class="mdi mdi-email-outline"></i> {{ $user->email }}</span>
            @if($user->institution)
            <span><i class="mdi mdi-office-building-outline"></i> {{ $user->institution->name }}</span>
            @endif
        </div>
        <div class="mob-card-footer">
            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                <i class="mdi mdi-eye-outline"></i> {{ __('messages.view') }}
            </a>
            @if(auth()->user()->hasRole('Admin') || (auth()->user()->hasRole('Cluster Coordinator') && in_array($user->getRoleNames()->first(), ['Gender Desk Coordinator', 'Gender Desk Member'])))
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-pencil-outline"></i>
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="tbl-empty">
        <i class="mdi mdi-account-off-outline"></i>
        <p>{{ __('messages.no_users_found', ['default' => 'No users found']) }}</p>
    </div>
    @endforelse

    @if(method_exists($users, 'hasPages') && $users->hasPages())
    <div class="mt-3">
        {{ $users->links('vendor.pagination.bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
