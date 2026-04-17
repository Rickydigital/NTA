@extends('components.main-layout')

@section('title', 'Courses')
@section('page-title', 'Courses')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Courses</h4>
                <small class="text-muted">Manage courses under each program level</small>
            </div>

            @can('course.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                + Add Course
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('courses.index') }}">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search course, code, level or program">
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

                    <div class="col-md-3">
                        <select name="program_level_id" class="form-select">
                            <option value="">All Levels</option>
                            @foreach($programLevels as $level)
                                <option value="{{ $level->id }}" {{ request('program_level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->program?->name }} - {{ $level->name }}
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
                            <th>Level</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Credit Hours</th>
                            <th>Description</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>{{ $loop->iteration + ($courses->firstItem() - 1) }}</td>
                                <td>{{ $course->programLevel?->program?->name ?? '-' }}</td>
                                <td>{{ $course->programLevel?->name ?? '-' }}</td>
                                <td><strong>{{ $course->code }}</strong></td>
                                <td>{{ $course->name }}</td>
                                <td>{{ rtrim(rtrim(number_format($course->credit_hours, 2), '0'), '.') }}</td>
                                <td>{{ $course->description ?: '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('course.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editCourseModal{{ $course->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('course.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('courses.destroy', $course) }}"
                                                      onsubmit="return confirm('Delete this course?')">
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

                            @can('course.update')
                            <div class="modal fade" id="editCourseModal{{ $course->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('courses.update', $course) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Course</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Program Level</label>
                                                        <select name="program_level_id" class="form-select" required>
                                                            <option value="">Select Level</option>
                                                            @foreach($programLevels as $level)
                                                                <option value="{{ $level->id }}" {{ $course->program_level_id == $level->id ? 'selected' : '' }}>
                                                                    {{ $level->program?->name }} - {{ $level->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Code</label>
                                                        <input type="text"
                                                               name="code"
                                                               class="form-control"
                                                               value="{{ $course->code }}"
                                                               required>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Credit Hours</label>
                                                        <input type="number"
                                                               name="credit_hours"
                                                               class="form-control"
                                                               min="0"
                                                               step="0.01"
                                                               value="{{ $course->credit_hours }}">
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label">Name</label>
                                                        <input type="text"
                                                               name="name"
                                                               class="form-control"
                                                               value="{{ $course->name }}"
                                                               required>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description"
                                                                  class="form-control"
                                                                  rows="3">{{ $course->description }}</textarea>
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
                                <td colspan="8" class="text-center text-muted py-4">No courses found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $courses->links() }}
        </div>
    </div>
</div>

@can('course.create')
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('courses.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Course</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Program Level</label>
                            <select name="program_level_id" class="form-select" required>
                                <option value="">Select Level</option>
                                @foreach($programLevels as $level)
                                    <option value="{{ $level->id }}" {{ old('program_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->program?->name }} - {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Code</label>
                            <input type="text"
                                   name="code"
                                   class="form-control"
                                   value="{{ old('code') }}"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Credit Hours</label>
                            <input type="number"
                                   name="credit_hours"
                                   class="form-control"
                                   min="0"
                                   step="0.01"
                                   value="{{ old('credit_hours', 0) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="3">{{ old('description') }}</textarea>
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