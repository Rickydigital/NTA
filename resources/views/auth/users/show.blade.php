@extends('components.main-layout')

@section('title', __('messages.view_user'))

@push('styles')
<style>
    .form-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 24px;
        margin-bottom: 20px;
    }
    .form-card h5 {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #3b82f6;
        margin-bottom: 16px;
        padding-bottom: 6px;
        border-bottom: 2px solid #dbeafe;
    }
    .detail-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 3px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .detail-value {
        font-size: 0.875rem;
        color: #1e293b;
        margin-bottom: 0;
    }
    .detail-value.muted { color: #94a3b8; font-style: italic; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h4 class="page-title">{{ __('messages.view_user') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('messages.users') }}</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>
        </nav>
    </div>
    <span class="ref-pill">{{ $user->name }}</span>
</div>

{{-- User Information --}}
<div class="form-card">
    <h5>{{ __('messages.user_information') }}</h5>
    <div class="row">
        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.modal.full_name') }}</p>
            <p class="detail-value">{{ $user->name }}</p>
        </div>

        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.modal.email') }}</p>
            <p class="detail-value">{{ $user->email }}</p>
        </div>

        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.modal.phone_number') }}</p>
            <p class="detail-value {{ $user->phone_number ? '' : 'muted' }}">
                {{ $user->phone_number ?? __('messages.not_provided') }}
            </p>
        </div>

        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.table_headers.gender') }}</p>
            <p class="detail-value {{ $user->gender ? '' : 'muted' }}">
                {{ $user->gender ?? __('messages.not_provided') }}
            </p>
        </div>

        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.table_headers.status_label') }}</p>
            <p class="detail-value">
                @if(strtolower($user->status) === 'active')
                    <span class="s-badge s-success">{{ __('messages.active') }}</span>
                @else
                    <span class="s-badge s-danger">{{ __('messages.deactivated') }}</span>
                @endif
            </p>
        </div>
    </div>
</div>

{{-- Role & Assignment --}}
<div class="form-card">
    <h5>{{ __('messages.role_and_assignment') }}</h5>
    <div class="row">
        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.roles') }}</p>
            <p class="detail-value {{ $user->getRoleNames()->isEmpty() ? 'muted' : '' }}">
                {{ $user->getRoleNames()->implode(', ') ?: __('messages.not_provided') }}
            </p>
        </div>

        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.table_headers.sector_name') }}</p>
            <p class="detail-value {{ $user->sector ? '' : 'muted' }}">
                {{ $user->sector?->name ?? __('messages.not_provided') }}
            </p>
        </div>

        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.table_headers.institution_name') }}</p>
            <p class="detail-value {{ $user->institution ? '' : 'muted' }}">
                @if($user->institution)
                    {{ $user->institution->name }}
                    @if($user->institution->ward)
                        <span class="text-muted">({{ $user->institution->ward->name }})</span>
                    @endif
                @else
                    {{ __('messages.not_provided') }}
                @endif
            </p>
        </div>

        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.region') }}</p>
            <p class="detail-value {{ $user->region ? '' : 'muted' }}">
                {{ $user->region?->name ?? __('messages.not_provided') }}
            </p>
        </div>

        <div class="col-md-4 mb-4">
            <p class="detail-label">{{ __('messages.cluster') }}</p>
            <p class="detail-value {{ $user->institution?->ward?->district?->region?->cluster ? '' : 'muted' }}">
                {{ $user->institution?->ward?->district?->region?->cluster?->name ?? __('messages.not_provided') }}
            </p>
        </div>
    </div>
</div>

{{-- Actions --}}
<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
        <i class="mdi mdi-arrow-left mr-1"></i>{{ __('messages.back') }}
    </a>

    @can('Manage users')
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-pencil mr-1"></i>{{ __('messages.edit_user') }}
        </a>
    @endcan

    @if(Auth::user()->hasRole('Admin'))
        <form action="{{ route('users.toggleStatus', $user->user_id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm {{ strtolower($user->status) === 'active' ? 'btn-danger' : 'btn-success' }}">
                <i class="mdi mdi-{{ strtolower($user->status) === 'active' ? 'account-off' : 'account-check' }} mr-1"></i>
                {{ strtolower($user->status) === 'active' ? __('messages.deactivate') : __('messages.activate') }}
            </button>
        </form>
    @endif
</div>

@endsection
