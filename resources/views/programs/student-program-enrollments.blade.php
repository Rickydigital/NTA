@extends('components.main-layout')

@section('title', 'Student Program Enrollments')
@section('page-title', 'Student Program Enrollments')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Student Program Enrollments</h4>
                <small class="text-muted">Manage student program enrollment history</small>
            </div>

            @can('student.update')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEnrollmentModal">
                + Add Enrollment
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('student-program-enrollments.index') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search student, reg no, program">
                    </div>

                    <div class="col-md-3">
                        <select name="student_id" class="form-select">
                            <option value="">All Students</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->reg_no }} - {{ $student->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="program_id" class="form-select">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="deferred" {{ request('status') === 'deferred' ? 'selected' : '' }}>Deferred</option>
                            <option value="discontinued" {{ request('status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
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
                            <th>Student</th>
                            <th>Reg No</th>
                            <th>Program</th>
                            <th>Intake Year</th>
                            <th>Enrollment Date</th>
                            <th>Completion Date</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td>{{ $loop->iteration + ($enrollments->firstItem() - 1) }}</td>
                                <td>{{ $enrollment->student?->full_name ?? '-' }}</td>
                                <td>{{ $enrollment->student?->reg_no ?? '-' }}</td>
                                <td>{{ $enrollment->program?->name ?? '-' }}</td>
                                <td>{{ $enrollment->intakeAcademicYear?->name ?? '-' }}</td>
                                <td>{{ optional($enrollment->enrollment_date)->format('d M Y') ?: '-' }}</td>
                                <td>{{ optional($enrollment->completion_date)->format('d M Y') ?: '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($enrollment->status) }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">⋮</button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('student.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editEnrollmentModal{{ $enrollment->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('student.update')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('student-program-enrollments.destroy', $enrollment) }}"
                                                      onsubmit="return confirm('Delete this enrollment?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            @can('student.update')
                            <div class="modal fade" id="editEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('student-program-enrollments.update', $enrollment) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Program Enrollment</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Student</label>
                                                        <select name="student_id" class="form-select" required>
                                                            @foreach($students as $student)
                                                                <option value="{{ $student->id }}" {{ $enrollment->student_id == $student->id ? 'selected' : '' }}>
                                                                    {{ $student->reg_no }} - {{ $student->full_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Program</label>
                                                        <select name="program_id" class="form-select" required>
                                                            @foreach($programs as $program)
                                                                <option value="{{ $program->id }}" {{ $enrollment->program_id == $program->id ? 'selected' : '' }}>
                                                                    {{ $program->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Intake Academic Year</label>
                                                        <select name="intake_academic_year_id" class="form-select">
                                                            <option value="">Select Year</option>
                                                            @foreach($academicYears as $year)
                                                                <option value="{{ $year->id }}" {{ $enrollment->intake_academic_year_id == $year->id ? 'selected' : '' }}>
                                                                    {{ $year->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Enrollment Date</label>
                                                        <input type="date" name="enrollment_date" class="form-control"
                                                               value="{{ optional($enrollment->enrollment_date)->format('Y-m-d') }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Completion Date</label>
                                                        <input type="date" name="completion_date" class="form-control"
                                                               value="{{ optional($enrollment->completion_date)->format('Y-m-d') }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="active" {{ $enrollment->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="completed" {{ $enrollment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="deferred" {{ $enrollment->status === 'deferred' ? 'selected' : '' }}>Deferred</option>
                                                            <option value="discontinued" {{ $enrollment->status === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                                        </select>
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
                                <td colspan="9" class="text-center text-muted py-4">No program enrollments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $enrollments->links() }}
        </div>
    </div>
</div>

@can('student.update')
<div class="modal fade" id="createEnrollmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('student-program-enrollments.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Program Enrollment</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Student</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->reg_no }} - {{ $student->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Program</label>
                            <select name="program_id" class="form-select" required>
                                <option value="">Select Program</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Intake Academic Year</label>
                            <select name="intake_academic_year_id" class="form-select">
                                <option value="">Select Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('intake_academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Enrollment Date</label>
                            <input type="date" name="enrollment_date" class="form-control" value="{{ old('enrollment_date') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Completion Date</label>
                            <input type="date" name="completion_date" class="form-control" value="{{ old('completion_date') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="deferred" {{ old('status') === 'deferred' ? 'selected' : '' }}>Deferred</option>
                                <option value="discontinued" {{ old('status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
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