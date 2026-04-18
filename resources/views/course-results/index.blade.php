@extends('components.main-layout')

@section('title', 'Course Results')
@section('page-title', 'Course Results')

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
                <h4 class="mb-1">Course Results</h4>
                <small class="text-muted">Manage student results per course and exam session</small>
            </div>

            @can('result-entry.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseResultModal">
                + Add Course Result
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('course-results.index') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search student, exam no, course, grade">
                    </div>

                    <div class="col-md-2">
                        <select name="student_id" class="form-select">
                            <option value="">All Students</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id')==$student->id ? 'selected' : ''
                                }}>
                                {{ $student->reg_no }} - {{ $student->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="exam_session_id" class="form-select">
                            <option value="">All Sessions</option>
                            @foreach($examSessions as $session)
                            <option value="{{ $session->id }}" {{ request('exam_session_id')==$session->id ? 'selected'
                                : '' }}>
                                {{ $session->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="course_id" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id')==$course->id ? 'selected' : '' }}>
                                {{ $course->code }} - {{ $course->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1">
                        <select name="status" class="form-select">
                            <option value="">Status</option>
                            <option value="draft" {{ request('status')==='draft' ? 'selected' : '' }}>Draft</option>
                            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected
                            </option>
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
                            <th>Exam No</th>
                            <th>Course</th>
                            <th>Session</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                        <tr>
                            <td>{{ $loop->iteration + ($results->firstItem() - 1) }}</td>
                            <td>{{ $result->student?->full_name ?? '-' }}</td>
                            <td>{{ $result->studentExamNumber?->exam_no ?? '-' }}</td>
                            <td>{{ $result->course?->code }} - {{ $result->course?->name }}</td>
                            <td>{{ $result->examSession?->name ?? '-' }}</td>
                            <td>{{ $result->raw_score !== null ? number_format($result->raw_score, 2) : '-' }}</td>
                            <td><strong>{{ $result->grade?->grade_code ?? '-' }}</strong></td>
                            <td>{{ $result->comment_snapshot ?: '-' }}</td>
                            <td>
                                @if($result->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                                @elseif($result->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @else
                                <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">⋮</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('result-entry.update')
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#editCourseResultModal{{ $result->id }}">
                                                Edit
                                            </button>
                                        </li>
                                        @endcan

                                        @can('result-entry.approve')
                                        @if($result->status !== 'approved')
                                        <li>
                                            <form method="POST" action="{{ route('course-results.approve', $result) }}"
                                                onsubmit="return confirm('Approve this course result?')">
                                                @csrf
                                                <button class="dropdown-item text-success">
                                                    Approve
                                                </button>
                                            </form>
                                        </li>
                                        @else
                                        <li>
                                            <form method="POST"
                                                action="{{ route('course-results.unapprove', $result) }}"
                                                onsubmit="return confirm('Move this result back to draft?')">
                                                @csrf
                                                <button class="dropdown-item text-warning">
                                                    Unapprove
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endcan

                                        @can('result-entry.delete')
                                        <li>
                                            <form method="POST" action="{{ route('course-results.destroy', $result) }}"
                                                onsubmit="return confirm('Delete this course result?')">
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

                        @can('result-entry.update')
                        <div class="modal fade" id="editCourseResultModal{{ $result->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('course-results.update', $result) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5>Edit Course Result</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Student</label>
                                                    <select name="student_id" class="form-select" required>
                                                        @foreach($students as $student)
                                                        <option value="{{ $student->id }}" {{ $result->student_id ==
                                                            $student->id ? 'selected' : '' }}>
                                                            {{ $student->reg_no }} - {{ $student->full_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Exam Number</label>
                                                    <select name="student_exam_number_id" class="form-select" required>
                                                        @foreach($studentExamNumbers as $examNo)
                                                        <option value="{{ $examNo->id }}" {{ $result->
                                                            student_exam_number_id == $examNo->id ? 'selected' : '' }}>
                                                            {{ $examNo->exam_no }} - {{ $examNo->student?->full_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Course</label>
                                                    <select name="course_id" class="form-select" required>
                                                        @foreach($courses as $course)
                                                        <option value="{{ $course->id }}" {{ $result->course_id ==
                                                            $course->id ? 'selected' : '' }}>
                                                            {{ $course->code }} - {{ $course->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Exam Session</label>
                                                    <select name="exam_session_id" class="form-select" required>
                                                        @foreach($examSessions as $session)
                                                        <option value="{{ $session->id }}" {{ $result->exam_session_id
                                                            == $session->id ? 'selected' : '' }}>
                                                            {{ $session->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Grade</label>
                                                    <select name="grade_id" class="form-select" required>
                                                        @foreach($grades as $grade)
                                                        <option value="{{ $grade->id }}" {{ $result->grade_id ==
                                                            $grade->id ? 'selected' : '' }}>
                                                            {{ $grade->grade_code }} - {{ $grade->comment_label }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Raw Score</label>
                                                    <input type="number" step="0.01" min="0" name="raw_score"
                                                        class="form-control" value="{{ $result->raw_score }}">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="draft" {{ $result->status === 'draft' ?
                                                            'selected' : '' }}>Draft</option>
                                                        <option value="approved" {{ $result->status === 'approved' ?
                                                            'selected' : '' }}>Approved</option>
                                                        <option value="rejected" {{ $result->status === 'rejected' ?
                                                            'selected' : '' }}>Rejected</option>
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
                            <td colspan="10" class="text-center text-muted py-4">No course results found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $results->links() }}
        </div>
    </div>
</div>

@can('result-entry.create')
<div class="modal fade" id="createCourseResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('course-results.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Course Result</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Student</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id')==$student->id ? 'selected' : ''
                                    }}>
                                    {{ $student->reg_no }} - {{ $student->full_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Exam Number</label>
                            <select name="student_exam_number_id" class="form-select" required>
                                <option value="">Select Exam Number</option>
                                @foreach($studentExamNumbers as $examNo)
                                <option value="{{ $examNo->id }}" {{ old('student_exam_number_id')==$examNo->id ?
                                    'selected' : '' }}>
                                    {{ $examNo->exam_no }} - {{ $examNo->student?->full_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Course</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id')==$course->id ? 'selected' : '' }}>
                                    {{ $course->code }} - {{ $course->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Exam Session</label>
                            <select name="exam_session_id" class="form-select" required>
                                <option value="">Select Session</option>
                                @foreach($examSessions as $session)
                                <option value="{{ $session->id }}" {{ old('exam_session_id')==$session->id ? 'selected'
                                    : '' }}>
                                    {{ $session->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Grade</label>
                            <select name="grade_id" class="form-select" required>
                                <option value="">Select Grade</option>
                                @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id')==$grade->id ? 'selected' : '' }}>
                                    {{ $grade->grade_code }} - {{ $grade->comment_label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Raw Score</label>
                            <input type="number" step="0.01" min="0" name="raw_score" class="form-control"
                                value="{{ old('raw_score') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="draft" {{ old('status', 'draft' )==='draft' ? 'selected' : '' }}>Draft
                                </option>
                                <option value="approved" {{ old('status')==='approved' ? 'selected' : '' }}>Approved
                                </option>
                                <option value="rejected" {{ old('status')==='rejected' ? 'selected' : '' }}>Rejected
                                </option>
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