@extends('components.main-layout')

@section('title', 'Student Exam Numbers')
@section('page-title', 'Student Exam Numbers')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Student Exam Numbers</h4>
                <small class="text-muted">Manage normalized exam numbers for students</small>
            </div>

            @can('exam-number.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentExamNumberModal">
                + Add Exam Number
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('student-exam-numbers.index') }}">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search exam no, reg no or student name">
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

                    <div class="col-md-3">
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
                            <th>Student</th>
                            <th>Reg No</th>
                            <th>Program</th>
                            <th>Level</th>
                            <th>Exam No</th>
                            <th>Academic Year</th>
                            <th>Issued At</th>
                            <th>Current</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentExamNumbers as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($studentExamNumbers->firstItem() - 1) }}</td>
                                <td>{{ $item->student?->full_name ?? '-' }}</td>
                                <td>{{ $item->student?->reg_no ?? '-' }}</td>
                                <td>{{ $item->student?->program?->name ?? '-' }}</td>
                                <td>{{ $item->student?->programLevel?->name ?? '-' }}</td>
                                <td><strong>{{ $item->exam_no }}</strong></td>
                                <td>{{ $item->academicYear?->name ?? '-' }}</td>
                                <td>{{ optional($item->issued_at)->format('d M Y') ?: '-' }}</td>
                                <td>
                                    @if($item->is_current)
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
                                            @can('exam-number.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editStudentExamNumberModal{{ $item->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('exam-number.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('student-exam-numbers.destroy', $item) }}"
                                                      onsubmit="return confirm('Delete this exam number?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            @can('exam-number.update')
                            <div class="modal fade" id="editStudentExamNumberModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('student-exam-numbers.update', $item) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Student Exam Number</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Student</label>
                                                        <select name="student_id" class="form-select" required>
                                                            <option value="">Select Student</option>
                                                            @foreach($students as $student)
                                                                <option value="{{ $student->id }}" {{ $item->student_id == $student->id ? 'selected' : '' }}>
                                                                    {{ $student->reg_no }} - {{ $student->full_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Academic Year</label>
                                                        <select name="academic_year_id" class="form-select">
                                                            <option value="">Select Academic Year</option>
                                                            @foreach($academicYears as $academicYear)
                                                                <option value="{{ $academicYear->id }}" {{ $item->academic_year_id == $academicYear->id ? 'selected' : '' }}>
                                                                    {{ $academicYear->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Issued At</label>
                                                        <input type="date"
                                                               name="issued_at"
                                                               class="form-control"
                                                               value="{{ optional($item->issued_at)->format('Y-m-d') }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Exam Number</label>
                                                        <input type="text"
                                                               name="exam_no"
                                                               class="form-control"
                                                               value="{{ $item->exam_no }}"
                                                               required>
                                                    </div>

                                                    <div class="col-md-4 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   name="is_current"
                                                                   value="1"
                                                                   id="is_current_edit_{{ $item->id }}"
                                                                   {{ $item->is_current ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_current_edit_{{ $item->id }}">
                                                                Set as Current Exam Number
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
                                <td colspan="10" class="text-center text-muted py-4">No student exam numbers found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $studentExamNumbers->links() }}
        </div>
    </div>
</div>

@can('exam-number.create')
<div class="modal fade" id="createStudentExamNumberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('student-exam-numbers.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Student Exam Number</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
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

                        <div class="col-md-3">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}" {{ old('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                        {{ $academicYear->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Issued At</label>
                            <input type="date"
                                   name="issued_at"
                                   class="form-control"
                                   value="{{ old('issued_at') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Exam Number</label>
                            <input type="text"
                                   name="exam_no"
                                   class="form-control"
                                   value="{{ old('exam_no') }}"
                                   required>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_current"
                                       value="1"
                                       id="is_current_create"
                                       checked>
                                <label class="form-check-label" for="is_current_create">
                                    Set as Current Exam Number
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