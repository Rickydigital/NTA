@extends('components.main-layout')

@section('title', 'Exam Results')
@section('page-title', 'Exam Results')

@section('content')
<div class="container-fluid">

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Exam Results</h4>
                <small class="text-muted">Manage official exam summaries per student and session</small>
            </div>

            @can('result-summary.generate')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createExamResultModal">
                + Add Exam Result
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('exam-results.index') }}">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search student, exam no, comment, classification">
                    </div>

                    <div class="col-md-3">
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

                    <div class="col-md-3">
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

                    <div class="col-md-1">
                        <select name="published" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('published')==='1' ? 'selected' : '' }}>Pub</option>
                            <option value="0" {{ request('published')==='0' ? 'selected' : '' }}>Draft</option>
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
                            <th>Session</th>
                            <th>Courses</th>
                            <th>Total Points</th>
                            <th>GPA</th>
                            <th>Classification</th>
                            <th>Decision</th>
                            <th>Published</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($examResults as $result)
                        <tr>
                            <td>{{ $loop->iteration + ($examResults->firstItem() - 1) }}</td>
                            <td>{{ $result->student?->full_name ?? '-' }}</td>
                            <td>{{ $result->studentExamNumber?->exam_no ?? '-' }}</td>
                            <td>{{ $result->examSession?->name ?? '-' }}</td>
                            <td>{{ $result->total_courses }}</td>
                            <td>{{ number_format($result->total_grade_points, 2) }}</td>
                            <td><strong>{{ number_format($result->gpa, 2) }}</strong></td>
                            <td>{{ $result->gpaClassification?->classification_code ?? '-' }}</td>
                            <td>{{ $result->progression_decision ?: '-' }}</td>
                            <td>
                                @if($result->is_published)
                                <span class="badge bg-success">Published</span>
                                @else
                                <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">⋮</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('result-summary.generate')
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#editExamResultModal{{ $result->id }}">
                                                Edit
                                            </button>
                                        </li>
                                        @endcan

                                        @can('result-summary.publish')
                                        @if(!$result->is_published)
                                        <li>
                                            <form method="POST" action="{{ route('exam-results.publish', $result) }}"
                                                onsubmit="return confirm('Publish this exam result?')">
                                                @csrf
                                                <button class="dropdown-item text-success">
                                                    Publish
                                                </button>
                                            </form>
                                        </li>
                                        @else
                                        <li>
                                            <form method="POST" action="{{ route('exam-results.unpublish', $result) }}"
                                                onsubmit="return confirm('Unpublish this exam result?')">
                                                @csrf
                                                <button class="dropdown-item text-warning">
                                                    Unpublish
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                        @endcan

                                        @can('progression.execute')
                                        <li>
                                            <form method="POST"
                                                action="{{ route('exam-results.execute-progression', $result) }}"
                                                onsubmit="return confirm('Execute progression for this exam result?')">
                                                @csrf
                                                <button class="dropdown-item text-primary">
                                                    Execute Progression
                                                </button>
                                            </form>
                                        </li>
                                        @endcan

                                        @can('result-summary.publish')
                                        <li>
                                            <form method="POST" action="{{ route('exam-results.destroy', $result) }}"
                                                onsubmit="return confirm('Delete this exam result?')">
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

                        @can('result-summary.generate')
                        <div class="modal fade" id="generateExamResultModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('exam-results.generate') }}">
                                        @csrf

                                        <div class="modal-header">
                                            <h5>Generate Exam Result</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Student</label>
                                                    <select name="student_id" class="form-select" required>
                                                        <option value="">Select Student</option>
                                                        @foreach($students as $student)
                                                        <option value="{{ $student->id }}">
                                                            {{ $student->reg_no }} - {{ $student->full_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Exam Session</label>
                                                    <select name="exam_session_id" class="form-select" required>
                                                        <option value="">Select Exam Session</option>
                                                        @foreach($examSessions as $session)
                                                        <option value="{{ $session->id }}">
                                                            {{ $session->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="alert alert-info mt-3 mb-0">
                                                This will use approved course results only and generate or update the
                                                official exam result summary.
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary">Generate</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endcan

                        @can('result-summary.generate')
                        <div class="modal fade" id="editExamResultModal{{ $result->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('exam-results.update', $result) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5>Edit Exam Result</h5>
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
                                                    <label class="form-label">Level Placement</label>
                                                    <select name="student_level_placement_id" class="form-select"
                                                        required>
                                                        @foreach($studentLevelPlacements as $placement)
                                                        <option value="{{ $placement->id }}" {{ $result->
                                                            student_level_placement_id == $placement->id ? 'selected' :
                                                            '' }}>
                                                            {{ $placement->enrollment?->student?->reg_no }} - {{
                                                            $placement->programLevel?->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
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

                                                <div class="col-md-3">
                                                    <label class="form-label">GPA Classification</label>
                                                    <select name="gpa_classification_id" class="form-select">
                                                        <option value="">Select Classification</option>
                                                        @foreach($gpaClassifications as $classification)
                                                        <option value="{{ $classification->id }}" {{ $result->
                                                            gpa_classification_id == $classification->id ? 'selected' :
                                                            '' }}>
                                                            {{ $classification->classification_code }} - {{
                                                            $classification->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="form-label">Total Courses</label>
                                                    <input type="number" min="0" name="total_courses"
                                                        class="form-control" value="{{ $result->total_courses }}"
                                                        required>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="form-label">Total Points</label>
                                                    <input type="number" step="0.01" min="0" name="total_grade_points"
                                                        class="form-control" value="{{ $result->total_grade_points }}"
                                                        required>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="form-label">GPA</label>
                                                    <input type="number" step="0.01" min="0" name="gpa"
                                                        class="form-control" value="{{ $result->gpa }}" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Final Comment</label>
                                                    <input type="text" name="final_comment" class="form-control"
                                                        value="{{ $result->final_comment }}">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Progression Decision</label>
                                                    <input type="text" name="progression_decision" class="form-control"
                                                        value="{{ $result->progression_decision }}"
                                                        placeholder="proceed / retained / disco / completed">
                                                </div>

                                                <div class="col-md-3 d-flex align-items-end">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="is_published" value="1"
                                                            id="publish_result_{{ $result->id }}" {{
                                                            $result->is_published ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="publish_result_{{ $result->id }}">
                                                            Published
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
                            <td colspan="11" class="text-center text-muted py-4">No exam results found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $examResults->links() }}
        </div>
    </div>
</div>

@can('result-summary.generate')
<div class="modal fade" id="createExamResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('exam-results.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Exam Result</h5>
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
                            <label class="form-label">Level Placement</label>
                            <select name="student_level_placement_id" class="form-select" required>
                                <option value="">Select Placement</option>
                                @foreach($studentLevelPlacements as $placement)
                                <option value="{{ $placement->id }}" {{ old('student_level_placement_id')==$placement->
                                    id ? 'selected' : '' }}>
                                    {{ $placement->enrollment?->student?->reg_no }} - {{ $placement->programLevel?->name
                                    }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
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

                        <div class="col-md-3">
                            <label class="form-label">GPA Classification</label>
                            <select name="gpa_classification_id" class="form-select">
                                <option value="">Select Classification</option>
                                @foreach($gpaClassifications as $classification)
                                <option value="{{ $classification->id }}" {{
                                    old('gpa_classification_id')==$classification->id ? 'selected' : '' }}>
                                    {{ $classification->classification_code }} - {{ $classification->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Total Courses</label>
                            <input type="number" min="0" name="total_courses" class="form-control"
                                value="{{ old('total_courses', 0) }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Total Points</label>
                            <input type="number" step="0.01" min="0" name="total_grade_points" class="form-control"
                                value="{{ old('total_grade_points', 0) }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">GPA</label>
                            <input type="number" step="0.01" min="0" name="gpa" class="form-control"
                                value="{{ old('gpa', 0) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Final Comment</label>
                            <input type="text" name="final_comment" class="form-control"
                                value="{{ old('final_comment') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Progression Decision</label>
                            <input type="text" name="progression_decision" class="form-control"
                                value="{{ old('progression_decision') }}"
                                placeholder="proceed / retained / disco / completed">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                    id="publish_result_create" {{ old('is_published') ? 'checked' : '' }}>
                                <label class="form-check-label" for="publish_result_create">
                                    Published
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