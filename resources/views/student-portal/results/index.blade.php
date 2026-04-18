@extends('components.main-layout')

@section('title', 'My Results')
@section('page-title', 'My Results')
@section('page-subtitle', 'Published examination results')

@section('content')
<div class="container-fluid">

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="mb-1">My Published Results</h4>
                    <small class="text-muted">
                        {{ $student->full_name }} | Reg No: {{ $student->reg_no }}
                    </small>
                </div>

                <div class="text-muted">
                    Program: <strong>{{ $student->program?->name ?? '-' }}</strong><br>
                    Level: <strong>{{ $student->programLevel?->name ?? '-' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Session</th>
                            <th>Academic Year</th>
                            <th>Exam No</th>
                            <th>Level</th>
                            <th>Total Courses</th>
                            <th>Total Points</th>
                            <th>GPA</th>
                            <th>Classification</th>
                            <th>Decision</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                            <tr>
                                <td>{{ $loop->iteration + ($results->firstItem() - 1) }}</td>
                                <td>{{ $result->examSession?->name ?? '-' }}</td>
                                <td>{{ $result->examSession?->academicYear?->name ?? '-' }}</td>
                                <td>{{ $result->studentExamNumber?->exam_no ?? '-' }}</td>
                                <td>{{ $result->studentLevelPlacement?->programLevel?->name ?? '-' }}</td>
                                <td>{{ $result->total_courses }}</td>
                                <td>{{ number_format($result->total_grade_points, 2) }}</td>
                                <td><strong>{{ number_format($result->gpa, 2) }}</strong></td>
                                <td>{{ $result->gpaClassification?->classification_code ?? '-' }}</td>
                                <td>{{ $result->progression_decision ?: '-' }}</td>
                                <td>
                                    <a href="{{ route('student.portal.results.show', $result) }}"
                                       class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    No published results available yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $results->links() }}
        </div>
    </div>
</div>
@endsection