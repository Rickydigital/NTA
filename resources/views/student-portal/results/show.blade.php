@extends('components.main-layout')

@section('title', 'Result Details')
@section('page-title', 'Result Details')
@section('page-subtitle', 'Published session result')

@section('content')
<div class="container-fluid">

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-3">
                <div>
                    <h4 class="mb-1">{{ $student->full_name }}</h4>
                    <small class="text-muted">
                        Reg No: {{ $student->reg_no }} |
                        Exam No: {{ $examResult->studentExamNumber?->exam_no ?? '-' }}
                    </small>
                </div>

                <div class="text-muted">
                    Session: <strong>{{ $examResult->examSession?->name ?? '-' }}</strong><br>
                    Academic Year: <strong>{{ $examResult->examSession?->academicYear?->name ?? '-' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">Total Courses</small>
                    <h4 class="mb-0">{{ $examResult->total_courses }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">Total Points</small>
                    <h4 class="mb-0">{{ number_format($examResult->total_grade_points, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">GPA</small>
                    <h4 class="mb-0">{{ number_format($examResult->gpa, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">Classification</small>
                    <h4 class="mb-0">{{ $examResult->gpaClassification?->classification_code ?? '-' }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Program</small>
                    <strong>{{ $student->program?->name ?? '-' }}</strong>
                </div>

                <div class="col-md-4">
                    <small class="text-muted d-block">Level</small>
                    <strong>{{ $examResult->studentLevelPlacement?->programLevel?->name ?? '-' }}</strong>
                </div>

                <div class="col-md-4">
                    <small class="text-muted d-block">Progression Decision</small>
                    <strong>{{ $examResult->progression_decision ?: '-' }}</strong>
                </div>

                <div class="col-md-12">
                    <small class="text-muted d-block">Final Comment</small>
                    <strong>{{ $examResult->final_comment ?: '-' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Course Breakdown</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Grade Point</th>
                            <th>Comment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courseResults as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->course?->code ?? '-' }}</td>
                                <td>{{ $row->course?->name ?? '-' }}</td>
                                <td>{{ $row->raw_score !== null ? number_format($row->raw_score, 2) : '-' }}</td>
                                <td><strong>{{ $row->grade?->grade_code ?? '-' }}</strong></td>
                                <td>{{ $row->grade_point_snapshot !== null ? number_format($row->grade_point_snapshot, 2) : '-' }}</td>
                                <td>{{ $row->comment_snapshot ?: '-' }}</td>
                                <td>{{ ucfirst($row->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No course result details found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="{{ route('student.portal.results.index') }}" class="btn btn-light">
                    Back to Results
                </a>
            </div>
        </div>
    </div>
</div>
@endsection