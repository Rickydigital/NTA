@extends('components.main-layout')

@section('title', 'Grades')
@section('page-title', 'Grades')

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
                <h4 class="mb-1">Grades</h4>
                <small class="text-muted">Manage grading rules, points, and pass/fail logic</small>
            </div>

            @can('grade.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGradeModal">
                + Add Grade
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('grades.index') }}">
                <div class="row">
                    <div class="col-md-11">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search by grade code, comment or result status">
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
                            <th>Grade</th>
                            <th>Point</th>
                            <th>Min Score</th>
                            <th>Max Score</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>GPA</th>
                            <th>Pass</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades as $grade)
                            <tr>
                                <td>{{ $loop->iteration + ($grades->firstItem() - 1) }}</td>
                                <td><strong>{{ $grade->grade_code }}</strong></td>
                                <td>{{ number_format($grade->grade_point, 2) }}</td>
                                <td>{{ $grade->min_score !== null ? number_format($grade->min_score, 2) : '-' }}</td>
                                <td>{{ $grade->max_score !== null ? number_format($grade->max_score, 2) : '-' }}</td>
                                <td>{{ $grade->comment_label ?: '-' }}</td>
                                <td>{{ $grade->result_status ?: '-' }}</td>
                                <td>
                                    @if($grade->affects_gpa)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($grade->is_pass_grade)
                                        <span class="badge bg-primary">Pass</span>
                                    @else
                                        <span class="badge bg-danger">Fail</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('grade.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editGradeModal{{ $grade->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('grade.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('grades.destroy', $grade) }}"
                                                      onsubmit="return confirm('Delete this grade?')">
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

                            @can('grade.update')
                            <div class="modal fade" id="editGradeModal{{ $grade->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('grades.update', $grade) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Grade</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Grade Code</label>
                                                        <input type="text" name="grade_code" class="form-control" value="{{ $grade->grade_code }}" required>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Grade Point</label>
                                                        <input type="number" step="0.01" min="0" name="grade_point" class="form-control" value="{{ $grade->grade_point }}" required>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Min Score</label>
                                                        <input type="number" step="0.01" min="0" name="min_score" class="form-control" value="{{ $grade->min_score }}">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Max Score</label>
                                                        <input type="number" step="0.01" min="0" name="max_score" class="form-control" value="{{ $grade->max_score }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Comment Label</label>
                                                        <input type="text" name="comment_label" class="form-control" value="{{ $grade->comment_label }}" placeholder="PASS / FAIL / FAILS ORAL">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Result Status</label>
                                                        <input type="text" name="result_status" class="form-control" value="{{ $grade->result_status }}" placeholder="PASS / FAIL / SUPP / DISCO_TRIGGER">
                                                    </div>

                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   name="affects_gpa"
                                                                   value="1"
                                                                   id="affects_gpa_{{ $grade->id }}"
                                                                   {{ $grade->affects_gpa ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="affects_gpa_{{ $grade->id }}">
                                                                Affects GPA
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   name="is_pass_grade"
                                                                   value="1"
                                                                   id="is_pass_grade_{{ $grade->id }}"
                                                                   {{ $grade->is_pass_grade ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_pass_grade_{{ $grade->id }}">
                                                                Pass Grade
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
                                <td colspan="10" class="text-center text-muted py-4">No grades found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $grades->links() }}
        </div>
    </div>
</div>

@can('grade.create')
<div class="modal fade" id="createGradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('grades.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Grade</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Grade Code</label>
                            <input type="text" name="grade_code" class="form-control" value="{{ old('grade_code') }}" placeholder="A / B / C / D / F / C***" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Grade Point</label>
                            <input type="number" step="0.01" min="0" name="grade_point" class="form-control" value="{{ old('grade_point') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Min Score</label>
                            <input type="number" step="0.01" min="0" name="min_score" class="form-control" value="{{ old('min_score') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Max Score</label>
                            <input type="number" step="0.01" min="0" name="max_score" class="form-control" value="{{ old('max_score') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Comment Label</label>
                            <input type="text" name="comment_label" class="form-control" value="{{ old('comment_label') }}" placeholder="PASS / FAIL / FAILS ORAL">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Result Status</label>
                            <input type="text" name="result_status" class="form-control" value="{{ old('result_status') }}" placeholder="PASS / FAIL / SUPP / DISCO_TRIGGER">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="affects_gpa"
                                       value="1"
                                       id="create_affects_gpa"
                                       checked>
                                <label class="form-check-label" for="create_affects_gpa">
                                    Affects GPA
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_pass_grade"
                                       value="1"
                                       id="create_is_pass_grade">
                                <label class="form-check-label" for="create_is_pass_grade">
                                    Pass Grade
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