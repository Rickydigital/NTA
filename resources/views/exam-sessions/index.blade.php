@extends('components.main-layout')

@section('title', 'Exam Sessions')
@section('page-title', 'Exam Sessions')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Exam Sessions</h4>
                <small class="text-muted">Manage exam sittings and publication settings</small>
            </div>

            @can('exam-session.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createExamSessionModal">
                + Add Exam Session
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('exam-sessions.index') }}">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search exam session...">
                    </div>

                    <div class="col-md-5">
                        <select name="academic_year_id" class="form-select">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" {{ request('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                    {{ $academicYear->name }}
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
                            <th>Academic Year</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Entry Open</th>
                            <th>Published</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examSessions as $examSession)
                            <tr>
                                <td>{{ $loop->iteration + ($examSessions->firstItem() - 1) }}</td>
                                <td>{{ $examSession->academicYear?->name ?? '-' }}</td>
                                <td><strong>{{ $examSession->name }}</strong></td>
                                <td>{{ $examSession->session_type ?: '-' }}</td>
                                <td>{{ optional($examSession->start_date)->format('d M Y') ?: '-' }}</td>
                                <td>{{ optional($examSession->end_date)->format('d M Y') ?: '-' }}</td>
                                <td>
                                    @if($examSession->is_result_entry_open)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($examSession->is_published)
                                        <span class="badge bg-primary">Yes</span>
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
                                            @can('exam-session.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editExamSessionModal{{ $examSession->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('exam-session.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('exam-sessions.destroy', $examSession) }}"
                                                      onsubmit="return confirm('Delete this exam session?')">
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

                            @can('exam-session.update')
                            <div class="modal fade" id="editExamSessionModal{{ $examSession->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('exam-sessions.update', $examSession) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Exam Session</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Academic Year</label>
                                                        <select name="academic_year_id" class="form-select" required>
                                                            <option value="">Select Academic Year</option>
                                                            @foreach($academicYears as $academicYear)
                                                                <option value="{{ $academicYear->id }}" {{ $examSession->academic_year_id == $academicYear->id ? 'selected' : '' }}>
                                                                    {{ $academicYear->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Name</label>
                                                        <input type="text"
                                                               name="name"
                                                               class="form-control"
                                                               value="{{ $examSession->name }}"
                                                               placeholder="Semester I"
                                                               required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Type</label>
                                                        <input type="text"
                                                               name="session_type"
                                                               class="form-control"
                                                               value="{{ $examSession->session_type }}"
                                                               placeholder="semester / annual / supplementary">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Start Date</label>
                                                        <input type="date"
                                                               name="start_date"
                                                               class="form-control"
                                                               value="{{ optional($examSession->start_date)->format('Y-m-d') }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">End Date</label>
                                                        <input type="date"
                                                               name="end_date"
                                                               class="form-control"
                                                               value="{{ optional($examSession->end_date)->format('Y-m-d') }}">
                                                    </div>

                                                    <div class="col-md-4 d-flex align-items-end">
                                                        <div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input"
                                                                       type="checkbox"
                                                                       name="is_result_entry_open"
                                                                       value="1"
                                                                       id="entry_open_{{ $examSession->id }}"
                                                                       {{ $examSession->is_result_entry_open ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="entry_open_{{ $examSession->id }}">
                                                                    Result Entry Open
                                                                </label>
                                                            </div>

                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                       type="checkbox"
                                                                       name="is_published"
                                                                       value="1"
                                                                       id="published_{{ $examSession->id }}"
                                                                       {{ $examSession->is_published ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="published_{{ $examSession->id }}">
                                                                    Published
                                                                </label>
                                                            </div>
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
                                <td colspan="9" class="text-center text-muted py-4">No exam sessions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $examSessions->links() }}
        </div>
    </div>
</div>

@can('exam-session.create')
<div class="modal fade" id="createExamSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('exam-sessions.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Exam Session</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}" {{ old('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                        {{ $academicYear->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   placeholder="Semester I"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <input type="text"
                                   name="session_type"
                                   class="form-control"
                                   value="{{ old('session_type') }}"
                                   placeholder="semester / annual / supplementary">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date"
                                   name="start_date"
                                   class="form-control"
                                   value="{{ old('start_date') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date"
                                   name="end_date"
                                   class="form-control"
                                   value="{{ old('end_date') }}">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="is_result_entry_open"
                                           value="1"
                                           id="create_entry_open"
                                           {{ old('is_result_entry_open') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="create_entry_open">
                                        Result Entry Open
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="is_published"
                                           value="1"
                                           id="create_published"
                                           {{ old('is_published') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="create_published">
                                        Published
                                    </label>
                                </div>
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