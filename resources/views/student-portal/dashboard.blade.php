@extends('components.main-layout')

@section('title', 'Student Dashboard')
@section('page-title', 'Student Dashboard')
@section('page-subtitle', 'Welcome to your academic portal')

@section('content')
<div class="container-fluid">

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="mb-1">Welcome, {{ $student->full_name }}</h4>
                    <small class="text-muted">
                        Registration Number: {{ $student->reg_no }}
                    </small>
                </div>

                <div class="text-muted">
                    Program: <strong>{{ $student->program?->name ?? '-' }}</strong><br>
                    Level: <strong>{{ $student->programLevel?->name ?? '-' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Published Results</small>
                    <h3 class="mb-0">{{ $publishedResultsCount }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Current Program</small>
                    <h6 class="mb-0">{{ $student->program?->name ?? '-' }}</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted d-block mb-2">Current Level</small>
                    <h6 class="mb-0">{{ $student->programLevel?->name ?? '-' }}</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Quick Actions</h5>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('student.portal.results.index') }}" class="btn btn-primary">
                    My Results
                </a>

                <a href="{{ route('profile.edit') }}" class="btn btn-light border">
                    My Profile
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Latest Published Result</h5>

            @if($latestResult)
                <div class="row g-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Session</small>
                        <strong>{{ $latestResult->examSession?->name ?? '-' }}</strong>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted d-block">Academic Year</small>
                        <strong>{{ $latestResult->examSession?->academicYear?->name ?? '-' }}</strong>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted d-block">Exam Number</small>
                        <strong>{{ $latestResult->studentExamNumber?->exam_no ?? '-' }}</strong>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block">Total Courses</small>
                        <strong>{{ $latestResult->total_courses }}</strong>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block">Total Points</small>
                        <strong>{{ number_format($latestResult->total_grade_points, 2) }}</strong>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block">GPA</small>
                        <strong>{{ number_format($latestResult->gpa, 2) }}</strong>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block">Classification</small>
                        <strong>{{ $latestResult->gpaClassification?->classification_code ?? '-' }}</strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">Final Comment</small>
                        <strong>{{ $latestResult->final_comment ?: '-' }}</strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">Progression Decision</small>
                        <strong>{{ $latestResult->progression_decision ?: '-' }}</strong>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('student.portal.results.show', $latestResult) }}" class="btn btn-outline-primary btn-sm">
                        View Full Result
                    </a>
                </div>
            @else
                <div class="text-muted">
                    No published result available yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection