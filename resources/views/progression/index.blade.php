@extends('components.main-layout')

@section('title', 'Progression Rules')
@section('page-title', 'Progression Rules')

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
                <h4 class="mb-1">Progression Rules</h4>
                <small class="text-muted">Manage level progression decisions by program and GPA policy</small>
            </div>

            @can('progression-rule.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProgressionRuleModal">
                + Add Progression Rule
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('progression-rules.index') }}">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search program, level, decision, notes">
                    </div>

                    <div class="col-md-3">
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
                        <select name="decision" class="form-select">
                            <option value="">All Decisions</option>
                            <option value="proceed" {{ request('decision') === 'proceed' ? 'selected' : '' }}>Proceed</option>
                            <option value="retained" {{ request('decision') === 'retained' ? 'selected' : '' }}>Retained</option>
                            <option value="disco" {{ request('decision') === 'disco' ? 'selected' : '' }}>Disco</option>
                            <option value="manual_review" {{ request('decision') === 'manual_review' ? 'selected' : '' }}>Manual Review</option>
                            <option value="completed" {{ request('decision') === 'completed' ? 'selected' : '' }}>Completed</option>
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
                            <th>Program</th>
                            <th>From Level</th>
                            <th>To Level</th>
                            <th>Min GPA</th>
                            <th>Max Failed</th>
                            <th>Decision</th>
                            <th>Manual</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $rule)
                            <tr>
                                <td>{{ $loop->iteration + ($rules->firstItem() - 1) }}</td>
                                <td>{{ $rule->program?->name ?? '-' }}</td>
                                <td>{{ $rule->fromLevel?->name ?? '-' }}</td>
                                <td>{{ $rule->toLevel?->name ?? '-' }}</td>
                                <td>{{ $rule->min_gpa_required !== null ? number_format($rule->min_gpa_required, 2) : '-' }}</td>
                                <td>{{ $rule->max_failed_courses_allowed }}</td>
                                <td><span class="badge bg-secondary">{{ $rule->decision }}</span></td>
                                <td>
                                    @if($rule->requires_manual_approval)
                                        <span class="badge bg-warning text-dark">Yes</span>
                                    @else
                                        <span class="badge bg-success">No</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">⋮</button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('progression-rule.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editProgressionRuleModal{{ $rule->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('progression-rule.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('progression-rules.destroy', $rule) }}"
                                                      onsubmit="return confirm('Delete this progression rule?')">
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

                            @can('progression-rule.update')
                            <div class="modal fade" id="editProgressionRuleModal{{ $rule->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('progression-rules.update', $rule) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Progression Rule</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Program</label>
                                                        <select name="program_id" class="form-select" required>
                                                            @foreach($programs as $program)
                                                                <option value="{{ $program->id }}" {{ $rule->program_id == $program->id ? 'selected' : '' }}>
                                                                    {{ $program->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">From Level</label>
                                                        <select name="from_program_level_id" class="form-select" required>
                                                            @foreach($programLevels as $level)
                                                                <option value="{{ $level->id }}" {{ $rule->from_program_level_id == $level->id ? 'selected' : '' }}>
                                                                    {{ $level->program?->name }} - {{ $level->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">To Level</label>
                                                        <select name="to_program_level_id" class="form-select">
                                                            <option value="">None</option>
                                                            @foreach($programLevels as $level)
                                                                <option value="{{ $level->id }}" {{ $rule->to_program_level_id == $level->id ? 'selected' : '' }}>
                                                                    {{ $level->program?->name }} - {{ $level->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Min GPA Required</label>
                                                        <input type="number" step="0.01" min="0" name="min_gpa_required" class="form-control" value="{{ $rule->min_gpa_required }}">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Max Failed Courses</label>
                                                        <input type="number" min="0" name="max_failed_courses_allowed" class="form-control" value="{{ $rule->max_failed_courses_allowed }}">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Decision</label>
                                                        <select name="decision" class="form-select" required>
                                                            <option value="proceed" {{ $rule->decision === 'proceed' ? 'selected' : '' }}>Proceed</option>
                                                            <option value="retained" {{ $rule->decision === 'retained' ? 'selected' : '' }}>Retained</option>
                                                            <option value="disco" {{ $rule->decision === 'disco' ? 'selected' : '' }}>Disco</option>
                                                            <option value="manual_review" {{ $rule->decision === 'manual_review' ? 'selected' : '' }}>Manual Review</option>
                                                            <option value="completed" {{ $rule->decision === 'completed' ? 'selected' : '' }}>Completed</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <label class="form-label">Notes</label>
                                                        <textarea name="notes" class="form-control" rows="3">{{ $rule->notes }}</textarea>
                                                    </div>

                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="blocked_by_disco" value="1" id="blocked_by_disco_{{ $rule->id }}" {{ $rule->blocked_by_disco ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="blocked_by_disco_{{ $rule->id }}">Blocked by Disco</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="blocked_by_fail_oral" value="1" id="blocked_by_fail_oral_{{ $rule->id }}" {{ $rule->blocked_by_fail_oral ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="blocked_by_fail_oral_{{ $rule->id }}">Blocked by Fail Oral</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="requires_manual_approval" value="1" id="manual_approval_{{ $rule->id }}" {{ $rule->requires_manual_approval ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="manual_approval_{{ $rule->id }}">Requires Manual Approval</label>
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
                                <td colspan="9" class="text-center text-muted py-4">No progression rules found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $rules->links() }}
        </div>
    </div>
</div>

@can('progression-rule.create')
<div class="modal fade" id="createProgressionRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('progression-rules.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Progression Rule</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
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
                            <label class="form-label">From Level</label>
                            <select name="from_program_level_id" class="form-select" required>
                                <option value="">Select From Level</option>
                                @foreach($programLevels as $level)
                                    <option value="{{ $level->id }}" {{ old('from_program_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->program?->name }} - {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">To Level</label>
                            <select name="to_program_level_id" class="form-select">
                                <option value="">None</option>
                                @foreach($programLevels as $level)
                                    <option value="{{ $level->id }}" {{ old('to_program_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->program?->name }} - {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Min GPA Required</label>
                            <input type="number" step="0.01" min="0" name="min_gpa_required" class="form-control" value="{{ old('min_gpa_required') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Max Failed Courses</label>
                            <input type="number" min="0" name="max_failed_courses_allowed" class="form-control" value="{{ old('max_failed_courses_allowed', 0) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Decision</label>
                            <select name="decision" class="form-select" required>
                                <option value="">Select Decision</option>
                                <option value="proceed" {{ old('decision') === 'proceed' ? 'selected' : '' }}>Proceed</option>
                                <option value="retained" {{ old('decision') === 'retained' ? 'selected' : '' }}>Retained</option>
                                <option value="disco" {{ old('decision') === 'disco' ? 'selected' : '' }}>Disco</option>
                                <option value="manual_review" {{ old('decision') === 'manual_review' ? 'selected' : '' }}>Manual Review</option>
                                <option value="completed" {{ old('decision') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="blocked_by_disco" value="1" id="create_blocked_by_disco" checked>
                                <label class="form-check-label" for="create_blocked_by_disco">Blocked by Disco</label>
                            </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="blocked_by_fail_oral" value="1" id="create_blocked_by_fail_oral" checked>
                                <label class="form-check-label" for="create_blocked_by_fail_oral">Blocked by Fail Oral</label>
                            </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="requires_manual_approval" value="1" id="create_requires_manual_approval">
                                <label class="form-check-label" for="create_requires_manual_approval">Requires Manual Approval</label>
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