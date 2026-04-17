@extends('components.main-layout')

@section('title', 'Programs')
@section('page-title', 'Programs')

@section('content')
<div class="container-fluid">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- HEADER --}}
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Programs</h4>
                <small class="text-muted">Manage all academic programs</small>
            </div>

            @can('program.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProgramModal">
                + Add Program
            </button>
            @endcan
        </div>
    </div>

    {{-- SEARCH --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-11">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search programs...">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-dark w-100">Go</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($programs as $program)
                            <tr>
                                <td>{{ $loop->iteration + ($programs->firstItem() - 1) }}</td>

                                <td><strong>{{ $program->code }}</strong></td>

                                <td>{{ $program->name }}</td>

                                <td>{{ $program->description ?? '-' }}</td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            @can('program.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $program->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('program.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('programs.destroy', $program) }}"
                                                      onsubmit="return confirm('Delete program?')">
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

                            {{-- EDIT MODAL --}}
                            @can('program.update')
                            <div class="modal fade" id="editModal{{ $program->id }}">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <form method="POST" action="{{ route('programs.update', $program) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Program</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                <div class="row g-3">

                                                    <div class="col-md-4">
                                                        <label>Code</label>
                                                        <input type="text"
                                                               name="code"
                                                               class="form-control"
                                                               value="{{ $program->code }}"
                                                               required>
                                                    </div>

                                                    <div class="col-md-8">
                                                        <label>Name</label>
                                                        <input type="text"
                                                               name="name"
                                                               class="form-control"
                                                               value="{{ $program->name }}"
                                                               required>
                                                    </div>

                                                    <div class="col-12">
                                                        <label>Description</label>
                                                        <textarea name="description"
                                                                  class="form-control"
                                                                  rows="3">{{ $program->description }}</textarea>
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    No programs found
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{ $programs->links() }}

        </div>
    </div>

</div>

{{-- CREATE MODAL --}}
@can('program.create')
<div class="modal fade" id="createProgramModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('programs.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Program</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label>Code</label>
                            <input type="text"
                                   name="code"
                                   class="form-control"
                                   value="{{ old('code') }}"
                                   required>
                        </div>

                        <div class="col-md-8">
                            <label>Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name') }}"
                                   required>
                        </div>

                        <div class="col-12">
                            <label>Description</label>
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