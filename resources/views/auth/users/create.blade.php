@extends('components.main-layout')

@section('title', __('messages.create_user'))

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
    .form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
    }
    .form-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding-top: 4px;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h4 class="page-title">{{ __('messages.create_user') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('messages.users') }}</a></li>
                <li class="breadcrumb-item active">{{ __('messages.create_user') }}</li>
            </ol>
        </nav>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('users.store') }}">
    @csrf

    {{-- User Information --}}
    <div class="form-card">
        <h5>{{ __('messages.user_information') }}</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="name" class="form-label">{{ __('messages.modal.full_name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="phone_number" class="form-label">{{ __('messages.modal.phone_number') }}</label>
                <input type="text" name="phone_number" id="phone_number"
                       class="form-control @error('phone_number') is-invalid @enderror"
                       value="{{ old('phone_number') }}">
                @error('phone_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="gender" class="form-label">{{ __('messages.table_headers.gender') }}</label>
                <select name="gender" id="gender"
                        class="form-control @error('gender') is-invalid @enderror">
                    <option value="">{{ __('messages.modal.select_gender') }}</option>
                    <option value="Male"   {{ old('gender') == 'Male'   ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                </select>
                @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">{{ __('messages.modal.email') }} <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    {{-- Role & Assignment --}}
    <div class="form-card">
        <h5>{{ __('messages.role_and_assignment') }}</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="role" class="form-label">{{ __('messages.roles') }} <span class="text-danger">*</span></label>
                <select name="role" id="role"
                        class="form-control @error('role') is-invalid @enderror" required>
                    <option value="">{{ __('messages.modal.select_role') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="sector_id" class="form-label">{{ __('messages.table_headers.sector_name') }}</label>
                <select name="sector_id" id="sector_id"
                        class="form-control select2 @error('sector_id') is-invalid @enderror">
                    <option value="">{{ __('messages.select_sector') }}</option>
                    @foreach ($sectors as $sector)
                        <option value="{{ $sector->sector_id }}"
                            {{ old('sector_id') == $sector->sector_id ? 'selected' : '' }}>
                            {{ $sector->name }}
                        </option>
                    @endforeach
                </select>
                @error('sector_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="col-md-4 mb-3" id="institution-wrapper">
                <label for="institution_id" class="form-label" id="institution-label">
                    {{ __('messages.table_headers.institution_name') }}
                </label>
                <select name="institution_id" id="institution_id"
                        class="form-control select2 @error('institution_id') is-invalid @enderror"
                        disabled>
                    <option value="">{{ __('messages.modal.select_institution') }}</option>
                    @foreach ($institutions as $institution)
                        <option value="{{ $institution->institution_id }}"
                                data-sector-id="{{ $institution->sector_id }}"
                                data-region-id="{{ $institution->ward?->district?->region_id ?? '' }}"
                                data-cluster-id="{{ $institution->ward?->district?->region?->cluster_id ?? '' }}"
                                {{ old('institution_id') == $institution->institution_id ? 'selected' : '' }}>
                            {{ $institution->name }}
                            ({{ $institution->ward?->name ?? __('messages.not_provided') }})
                        </option>
                    @endforeach
                </select>
                @error('institution_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
            <i class="mdi mdi-arrow-left mr-1"></i>{{ __('messages.modal.cancel_button') }}
        </a>
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="mdi mdi-check mr-1"></i>{{ __('messages.create_user') }}
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
$(function () {
    $('#sector_id, #institution_id').select2({ width: '100%', allowClear: true });

    const roleSelect        = document.getElementById('role');
    const sectorSelect      = document.getElementById('sector_id');
    const institutionSelect = document.getElementById('institution_id');
    const institutionWrapper = document.getElementById('institution-wrapper');
    const institutionLabel  = document.getElementById('institution-label');

    const institutionRequiredRoles = ['Cluster Coordinator', 'Gender Desk Coordinator', 'Gender Desk Member', 'Head Of Institution'];
    const sectorOnlyRoles          = ['Admin', 'MoCDGWSG'];

    const originalOptions = Array.from(institutionSelect.options).map(o => o.cloneNode(true));

    function refreshSelect2() {
        if (window.jQuery && $(institutionSelect).hasClass('select2-hidden-accessible')) {
            $(institutionSelect).trigger('change.select2');
        }
    }

    function setInstitutionState() {
        const role     = roleSelect.value;
        const sectorId = sectorSelect.value;

        if (sectorOnlyRoles.includes(role)) {
            institutionWrapper.style.display = 'none';
            institutionSelect.value = '';
            institutionSelect.removeAttribute('required');
            institutionSelect.setAttribute('disabled', 'disabled');
            institutionLabel.innerHTML = "{{ __('messages.table_headers.institution_name') }}";
            refreshSelect2();
            return;
        }

        institutionWrapper.style.display = '';

        if (!sectorId) {
            institutionSelect.value = '';
            institutionSelect.setAttribute('disabled', 'disabled');
        } else {
            institutionSelect.removeAttribute('disabled');
        }

        if (institutionRequiredRoles.includes(role)) {
            institutionSelect.setAttribute('required', 'required');
            institutionLabel.innerHTML = "{{ __('messages.table_headers.institution_name') }} <span class='text-danger'>*</span>";
        } else {
            institutionSelect.removeAttribute('required');
            institutionLabel.innerHTML = "{{ __('messages.table_headers.institution_name') }}";
        }

        refreshSelect2();
    }

    function filterInstitutionsBySector() {
        const sectorId    = sectorSelect.value;
        const currentVal  = institutionSelect.value;

        institutionSelect.innerHTML = '';
        originalOptions.forEach(function (opt) {
            if (opt.value === '' || (sectorId && opt.getAttribute('data-sector-id') === sectorId)) {
                institutionSelect.appendChild(opt.cloneNode(true));
            }
        });

        institutionSelect[sectorId ? 'removeAttribute' : 'setAttribute']('disabled', 'disabled');

        const matched = Array.from(institutionSelect.options).find(o => o.value === currentVal);
        institutionSelect.value = matched ? currentVal : '';

        refreshSelect2();
    }

    $(roleSelect).on('change', function () {
        if (sectorOnlyRoles.includes(roleSelect.value)) institutionSelect.value = '';
        filterInstitutionsBySector();
        setInstitutionState();
    });

    $(sectorSelect).on('change', function () {
        institutionSelect.value = '';
        filterInstitutionsBySector();
        setInstitutionState();
    });

    filterInstitutionsBySector();
    setInstitutionState();
});
</script>
@endpush
