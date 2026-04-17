@extends('components.main-layout')

@section('title', 'Students')
@section('page-title', 'Students')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Students</h4>
                <small class="text-muted">Manage students by program and level</small>
            </div>

            @can('student.create')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                + Add Student
            </button>
            @endcan
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('students.index') }}">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search reg no, names, phone, email">
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
                            <th>Reg No</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Program</th>
                            <th>Level</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $loop->iteration + ($students->firstItem() - 1) }}</td>
                                <td><strong>{{ $student->reg_no }}</strong></td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->gender ? ucfirst($student->gender) : '-' }}</td>
                                <td>{{ $student->program?->name ?? '-' }}</td>
                                <td>{{ $student->programLevel?->name ?? '-' }}</td>
                                <td>{{ $student->phone_no ?: '-' }}</td>
                                <td>{{ $student->email ?: '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                            ⋮
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('student.update')
                                            <li>
                                                <button class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editStudentModal{{ $student->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            @endcan

                                            @can('student.delete')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('students.destroy', $student) }}"
                                                      onsubmit="return confirm('Delete this student?')">
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

                            @can('student.update')
                            <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('students.update', $student) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5>Edit Student</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Program</label>
                                                        <select name="program_id" class="form-select" required>
                                                            <option value="">Select Program</option>
                                                            @foreach($programs as $program)
                                                                <option value="{{ $program->id }}" {{ $student->program_id == $program->id ? 'selected' : '' }}>
                                                                    {{ $program->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Level</label>
                                                        <select name="program_level_id" class="form-select" required>
                                                            <option value="">Select Level</option>
                                                            @foreach($programLevels as $level)
                                                                <option value="{{ $level->id }}" {{ $student->program_level_id == $level->id ? 'selected' : '' }}>
                                                                    {{ $level->program?->name }} - {{ $level->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Registration No</label>
                                                        <input type="text" name="reg_no" class="form-control" value="{{ $student->reg_no }}" required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">First Name</label>
                                                        <input type="text" name="first_name" class="form-control" value="{{ $student->first_name }}" required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Second Name</label>
                                                        <input type="text" name="second_name" class="form-control" value="{{ $student->second_name }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Last Name</label>
                                                        <input type="text" name="last_name" class="form-control" value="{{ $student->last_name }}" required>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Gender</label>
                                                        <select name="gender" class="form-select">
                                                            <option value="">Select Gender</option>
                                                            <option value="male" {{ $student->gender === 'male' ? 'selected' : '' }}>Male</option>
                                                            <option value="female" {{ $student->gender === 'female' ? 'selected' : '' }}>Female</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Date of Birth</label>
                                                        <input type="date" name="date_of_birth" class="form-control" value="{{ optional($student->date_of_birth)->format('Y-m-d') }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Phone No</label>
                                                        <input type="text" name="phone_no" class="form-control" value="{{ $student->phone_no }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $student->email }}">
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
                                <td colspan="9" class="text-center text-muted py-4">No students found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $students->links() }}
        </div>
    </div>
</div>

@can('student.create')
<div class="modal fade" id="createStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('students.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Add Student</h5>
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
                            <label class="form-label">Level</label>
                            <select name="program_level_id" class="form-select" required>
                                <option value="">Select Level</option>
                                @foreach($programLevels as $level)
                                    <option value="{{ $level->id }}" {{ old('program_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->program?->name }} - {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Registration No</label>
                            <input type="text" name="reg_no" class="form-control" value="{{ old('reg_no') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Second Name</label>
                            <input type="text" name="second_name" class="form-control" value="{{ old('second_name') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Phone No</label>
                            <input type="text" name="phone_no" class="form-control" value="{{ old('phone_no') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
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