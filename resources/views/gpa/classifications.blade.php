@extends('components.main-layout')

@section('title', 'GPA Classifications')
@section('page-title', 'GPA Classifications')

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
                <h4 class="mb-1">GPA Classifications</h4>
                <small class="text-muted">Manage GPA ranges, comments, and progression actions</small>
            </div>

            @can('gpa-classification.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGpaClassificationModal">
                + Add GPA Classification
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('gpa-classifications.index') }}">
                <div class="row">
                    <div class="col-md-11">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search name, code, comment or action">
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
                            <th>Name</th>
                            <th>Code</th>
                            <th>Min GPA</th>
                            <th>Max GPA</th>
                            <th>Final Comment</th>
                            <th>Progression Action</th>
                            <th>Priority</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gpaClassifications as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($gpaClassifications->firstItem() - 1) }}</td>
                                <td>{{ $item->name }}</td>
                                <td><strong>{{ $item->classification_code }}</strong></td>
                                <td>{{ number_format($item->min_gpa, 2) }}</td>
                                <td>{{ number_format($item->max_gpa, 2) }}</td>
                                <td>{{ $item->final_comment }}</td>
                                <td>{{ $item->progression_action ?: '-' }}</td>
                                <td>{{ $item->priority_order }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('gpa-classification.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editGpaClassificationModal{{ $item->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('gpa-classification.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('gpa-classifications.destroy', $item) }}"
                                                      onsubmit="return confirm('Delete this GPA classification?')">
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

                            @can('gpa-classification.update')
                            <div class="modal fade" id="editGpaClassificationModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('gpa-classifications.update', $item) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit GPA Classification</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Code</label>
                                                        <input type="text" name="classification_code" class="form-control" value="{{ $item->classification_code }}" required>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="form-label">Min GPA</label>
                                                        <input type="number" step="0.01" min="0" name="min_gpa" class="form-control" value="{{ $item->min_gpa }}" required>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="form-label">Max GPA</label>
                                                        <input type="number" step="0.01" min="0" name="max_gpa" class="form-control" value="{{ $item->max_gpa }}" required>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="form-label">Priority</label>
                                                        <input type="number" min="0" name="priority_order" class="form-control" value="{{ $item->priority_order }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Final Comment</label>
                                                        <input type="text" name="final_comment" class="form-control" value="{{ $item->final_comment }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Progression Action</label>
                                                        <input type="text" name="progression_action" class="form-control" value="{{ $item->progression_action }}" placeholder="proceed / retained / disco / manual_review / completed">
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
                                <td colspan="9" class="text-center text-muted py-4">No GPA classifications found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $gpaClassifications->links() }}
        </div>
    </div>
</div>

@can('gpa-classification.create')
<div class="modal fade" id="createGpaClassificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('gpa-classifications.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add GPA Classification</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Pass / Fail / Disco" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Code</label>
                            <input type="text" name="classification_code" class="form-control" value="{{ old('classification_code') }}" placeholder="PASS / FAIL / DISCO" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Min GPA</label>
                            <input type="number" step="0.01" min="0" name="min_gpa" class="form-control" value="{{ old('min_gpa') }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Max GPA</label>
                            <input type="number" step="0.01" min="0" name="max_gpa" class="form-control" value="{{ old('max_gpa') }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Priority</label>
                            <input type="number" min="0" name="priority_order" class="form-control" value="{{ old('priority_order', 0) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Final Comment</label>
                            <input type="text" name="final_comment" class="form-control" value="{{ old('final_comment') }}" placeholder="Proceed to next level / Retained / Discontinued" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Progression Action</label>
                            <input type="text" name="progression_action" class="form-control" value="{{ old('progression_action') }}" placeholder="proceed / retained / disco / manual_review / completed">
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