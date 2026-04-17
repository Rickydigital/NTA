@extends('components.main-layout')

@section('title', 'Academic Years')
@section('page-title', 'Academic Years')

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
                <h4 class="mb-1">Academic Years</h4>
                <small class="text-muted">Manage academic year periods</small>
            </div>

            @can('academic-year.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAcademicYearModal">
                + Add Academic Year
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('academic-years.index') }}">
                <div class="row">
                    <div class="col-md-11">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search academic year...">
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
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Current</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicYears as $academicYear)
                            <tr>
                                <td>{{ $loop->iteration + ($academicYears->firstItem() - 1) }}</td>
                                <td><strong>{{ $academicYear->name }}</strong></td>
                                <td>{{ optional($academicYear->start_date)->format('d M Y') }}</td>
                                <td>{{ optional($academicYear->end_date)->format('d M Y') }}</td>
                                <td>
                                    @if($academicYear->is_current)
                                        <span class="badge bg-success">Current</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('academic-year.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editAcademicYearModal{{ $academicYear->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('academic-year.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('academic-years.destroy', $academicYear) }}"
                                                      onsubmit="return confirm('Delete this academic year?')">
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

                            @can('academic-year.update')
                            <div class="modal fade" id="editAcademicYearModal{{ $academicYear->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('academic-years.update', $academicYear) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Academic Year</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Name</label>
                                                        <input type="text"
                                                               name="name"
                                                               class="form-control"
                                                               value="{{ $academicYear->name }}"
                                                               placeholder="2025/2026"
                                                               required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Start Date</label>
                                                        <input type="date"
                                                               name="start_date"
                                                               class="form-control"
                                                               value="{{ optional($academicYear->start_date)->format('Y-m-d') }}"
                                                               required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">End Date</label>
                                                        <input type="date"
                                                               name="end_date"
                                                               class="form-control"
                                                               value="{{ optional($academicYear->end_date)->format('Y-m-d') }}"
                                                               required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-check mt-3">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   name="is_current"
                                                                   value="1"
                                                                   id="is_current_edit_{{ $academicYear->id }}"
                                                                   {{ $academicYear->is_current ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_current_edit_{{ $academicYear->id }}">
                                                                Set as Current Academic Year
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
                                <td colspan="6" class="text-center text-muted py-4">No academic years found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $academicYears->links() }}
        </div>
    </div>
</div>

@can('academic-year.create')
<div class="modal fade" id="createAcademicYearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('academic-years.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Academic Year</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   placeholder="2025/2026"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date"
                                   name="start_date"
                                   class="form-control"
                                   value="{{ old('start_date') }}"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date"
                                   name="end_date"
                                   class="form-control"
                                   value="{{ old('end_date') }}"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <div class="form-check mt-3">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_current"
                                       value="1"
                                       id="is_current_create"
                                       {{ old('is_current') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_current_create">
                                    Set as Current Academic Year
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