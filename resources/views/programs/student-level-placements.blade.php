@extends('components.main-layout')

@section('title', 'Student Level Placements')
@section('page-title', 'Student Level Placements')

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
                <h4 class="mb-1">Student Level Placements</h4>
                <small class="text-muted">Track student level history and current placements</small>
            </div>

            @can('student.update')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlacementModal">
                + Add Placement
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('student-level-placements.index') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search student, level, reason, status">
                    </div>

                    <div class="col-md-3">
                        <select name="student_program_enrollment_id" class="form-select">
                            <option value="">All Enrollments</option>
                            @foreach($enrollments as $enrollment)
                                <option value="{{ $enrollment->id }}" {{ request('student_program_enrollment_id') == $enrollment->id ? 'selected' : '' }}>
                                    {{ $enrollment->student?->reg_no }} - {{ $enrollment->student?->full_name }} - {{ $enrollment->program?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="program_level_id" class="form-select">
                            <option value="">All Levels</option>
                            @foreach($programLevels as $level)
                                <option value="{{ $level->id }}" {{ request('program_level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->program?->name }} - {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="current" class="form-select">
                            <option value="">Current / Old</option>
                            <option value="1" {{ request('current') === '1' ? 'selected' : '' }}>Current</option>
                            <option value="0" {{ request('current') === '0' ? 'selected' : '' }}>Old</option>
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
                            <th>Level</th>
                            <th>Academic Year</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Current</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($placements as $placement)
                            <tr>
                                <td>{{ $loop->iteration + ($placements->firstItem() - 1) }}</td>
                                <td>{{ $placement->enrollment?->student?->full_name ?? '-' }}</td>
                                <td>{{ $placement->enrollment?->student?->reg_no ?? '-' }}</td>
                                <td>{{ $placement->enrollment?->program?->name ?? '-' }}</td>
                                <td>{{ $placement->programLevel?->name ?? '-' }}</td>
                                <td>{{ $placement->academicYear?->name ?? '-' }}</td>
                                <td>{{ optional($placement->start_date)->format('d M Y') ?: '-' }}</td>
                                <td>{{ optional($placement->end_date)->format('d M Y') ?: '-' }}</td>
                                <td>
                                    @if($placement->is_current)
                                        <span class="badge bg-success">Current</span>
                                    @else
                                        <span class="badge bg-secondary">Old</span>
                                    @endif
                                </td>
                                <td>{{ $placement->progression_status ?: '-' }}</td>
                                <td>{{ $placement->placement_reason ?: '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">⋮</button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('student.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editPlacementModal{{ $placement->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('student.update')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('student-level-placements.destroy', $placement) }}"
                                                      onsubmit="return confirm('Delete this placement?')">
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
                            <div class="modal fade" id="editPlacementModal{{ $placement->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('student-level-placements.update', $placement) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Level Placement</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Enrollment</label>
                                                        <select name="student_program_enrollment_id" class="form-select" required>
                                                            @foreach($enrollments as $enrollment)
                                                                <option value="{{ $enrollment->id }}" {{ $placement->student_program_enrollment_id == $enrollment->id ? 'selected' : '' }}>
                                                                    {{ $enrollment->student?->reg_no }} - {{ $enrollment->student?->full_name }} - {{ $enrollment->program?->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Level</label>
                                                        <select name="program_level_id" class="form-select" required>
                                                            @foreach($programLevels as $level)
                                                                <option value="{{ $level->id }}" {{ $placement->program_level_id == $level->id ? 'selected' : '' }}>
                                                                    {{ $level->program?->name }} - {{ $level->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Academic Year</label>
                                                        <select name="academic_year_id" class="form-select">
                                                            <option value="">Select Year</option>
                                                            @foreach($academicYears as $year)
                                                                <option value="{{ $year->id }}" {{ $placement->academic_year_id == $year->id ? 'selected' : '' }}>
                                                                    {{ $year->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Start Date</label>
                                                        <input type="date"
                                                               name="start_date"
                                                               class="form-control"
                                                               value="{{ optional($placement->start_date)->format('Y-m-d') }}">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">End Date</label>
                                                        <input type="date"
                                                               name="end_date"
                                                               class="form-control"
                                                               value="{{ optional($placement->end_date)->format('Y-m-d') }}">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Progression Status</label>
                                                        <input type="text"
                                                               name="progression_status"
                                                               class="form-control"
                                                               value="{{ $placement->progression_status }}"
                                                               placeholder="proceed / retained / completed">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Placement Reason</label>
                                                        <input type="text"
                                                               name="placement_reason"
                                                               class="form-control"
                                                               value="{{ $placement->placement_reason }}"
                                                               placeholder="initial / auto_progression / manual_update">
                                                    </div>

                                                    <div class="col-md-4 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   name="is_current"
                                                                   value="1"
                                                                   id="placement_current_{{ $placement->id }}"
                                                                   {{ $placement->is_current ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="placement_current_{{ $placement->id }}">
                                                                Set as Current Placement
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
                                <td colspan="12" class="text-center text-muted py-4">No level placements found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $placements->links() }}
        </div>
    </div>
</div>

@can('student.update')
<div class="modal fade" id="createPlacementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('student-level-placements.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Level Placement</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Enrollment</label>
                            <select name="student_program_enrollment_id" class="form-select" required>
                                <option value="">Select Enrollment</option>
                                @foreach($enrollments as $enrollment)
                                    <option value="{{ $enrollment->id }}" {{ old('student_program_enrollment_id') == $enrollment->id ? 'selected' : '' }}>
                                        {{ $enrollment->student?->reg_no }} - {{ $enrollment->student?->full_name }} - {{ $enrollment->program?->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Level</label>
                            <select name="program_level_id" class="form-select" required>
                                <option value="">Select Level</option>
                                @foreach($programLevels as $level)
                                    <option value="{{ $level->id }}" {{ old('program_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->program?->name }} - {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                <option value="">Select Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Progression Status</label>
                            <input type="text" name="progression_status" class="form-control" value="{{ old('progression_status') }}" placeholder="proceed / retained / completed">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Placement Reason</label>
                            <input type="text" name="placement_reason" class="form-control" value="{{ old('placement_reason', 'initial') }}" placeholder="initial / auto_progression / manual_update">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_current"
                                       value="1"
                                       id="placement_current_create"
                                       checked>
                                <label class="form-check-label" for="placement_current_create">
                                    Set as Current Placement
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