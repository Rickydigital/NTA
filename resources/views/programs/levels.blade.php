@extends('components.main-layout')

@section('title', 'Program Levels')
@section('page-title', 'Program Levels')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Program Levels</h4>
                <small class="text-muted">Manage NTA levels under each program</small>
            </div>

            @can('program-level.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLevelModal">
                + Add Level
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('program-levels.index') }}">
                <div class="row g-2">
                    <div class="col-md-7">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search level, code, number or program">
                    </div>

                    <div class="col-md-4">
                        <select name="program_id" class="form-select">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
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
                            <th>Program</th>
                            <th>Level Name</th>
                            <th>Code</th>
                            <th>Level No</th>
                            <th>Order</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($levels as $level)
                            <tr>
                                <td>{{ $loop->iteration + ($levels->firstItem() - 1) }}</td>
                                <td>{{ $level->program->name ?? '-' }}</td>
                                <td>{{ $level->name }}</td>
                                <td><strong>{{ $level->code }}</strong></td>
                                <td>{{ $level->level_number }}</td>
                                <td>{{ $level->sort_order }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('program-level.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editLevelModal{{ $level->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('program-level.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('program-levels.destroy', $level) }}"
                                                      onsubmit="return confirm('Delete this level?')">
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

                            @can('program-level.update')
                            <div class="modal fade" id="editLevelModal{{ $level->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('program-levels.update', $level) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Program Level</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Program</label>
                                                        <select name="program_id" class="form-select" required>
                                                            <option value="">Select Program</option>
                                                            @foreach($programs as $program)
                                                                <option value="{{ $program->id }}" {{ $level->program_id == $program->id ? 'selected' : '' }}>
                                                                    {{ $program->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Level Name</label>
                                                        <input type="text"
                                                               name="name"
                                                               class="form-control"
                                                               value="{{ $level->name }}"
                                                               required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Code</label>
                                                        <input type="text"
                                                               name="code"
                                                               class="form-control"
                                                               value="{{ $level->code }}"
                                                               required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Level Number</label>
                                                        <input type="number"
                                                               name="level_number"
                                                               class="form-control"
                                                               value="{{ $level->level_number }}"
                                                               min="1"
                                                               required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Sort Order</label>
                                                        <input type="number"
                                                               name="sort_order"
                                                               class="form-control"
                                                               value="{{ $level->sort_order }}"
                                                               min="0">
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
                                <td colspan="7" class="text-center text-muted py-4">No program levels found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $levels->links() }}
        </div>
    </div>
</div>

@can('program-level.create')
<div class="modal fade" id="createLevelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('program-levels.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Program Level</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
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

                        <div class="col-md-6">
                            <label class="form-label">Level Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   placeholder="Example: NTA Level 4"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Code</label>
                            <input type="text"
                                   name="code"
                                   class="form-control"
                                   value="{{ old('code') }}"
                                   placeholder="NTA 4"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Level Number</label>
                            <input type="number"
                                   name="level_number"
                                   class="form-control"
                                   value="{{ old('level_number') }}"
                                   min="1"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Sort Order</label>
                            <input type="number"
                                   name="sort_order"
                                   class="form-control"
                                   value="{{ old('sort_order') }}"
                                   min="0"
                                   placeholder="Auto from level no">
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