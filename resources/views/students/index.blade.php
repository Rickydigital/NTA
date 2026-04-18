@extends('components.main-layout')

@section('title', 'Students')
@section('page-title', 'Students')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-1">Students</h4>
                <small class="text-muted">Manage students and portal accounts</small>
            </div>

            <div class="d-flex gap-2">
                @can('student.create')
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importStudentsModal">
                    Import Students
                </button>
                @endcan

                @can('student.create')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentModal">
                    + Add Student
                </button>
                @endcan
            </div>
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

        <form method="POST" action="{{ route('students.bulk-reset-passwords') }}" onsubmit="return confirm('Reset selected student passwords to registration numbers?')">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="text-muted">
                    Select students and reset portal passwords in bulk.
                </div>

                @can('student.update')
                <button type="submit" class="btn btn-warning btn-sm">
                    Reset Selected Passwords
                </button>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="check-all-students">
                            </th>
                            <th>#</th>
                            <th>Reg No</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Program</th>
                            <th>Level</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Portal</th>
                            <th width="140">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox">
                                </td>
                                <td>{{ $loop->iteration + ($students->firstItem() - 1) }}</td>
                                <td><strong>{{ $student->reg_no }}</strong></td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->gender ? ucfirst($student->gender) : '-' }}</td>
                                <td>{{ $student->program?->name ?? '-' }}</td>
                                <td>{{ $student->programLevel?->name ?? '-' }}</td>
                                <td>{{ $student->phone_no ?: '-' }}</td>
                                <td>{{ $student->email ?: '-' }}</td>
                                <td>
                                    @if($student->user)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">None</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">⋮</button>
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

                                            @can('student.update')
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('students.portal-account', $student) }}"
                                                      onsubmit="return confirm('Create or update portal account for this student?')">
                                                    @csrf
                                                    <button class="dropdown-item text-primary">
                                                        {{ $student->user ? 'Update Portal Account' : 'Create Portal Account' }}
                                                    </button>
                                                </form>
                                            </li>
                                            @endcan

                                            @can('student.update')
                                            @if($student->user)
                                            <li>
                                                <form method="POST"
                                                      action="{{ route('students.reset-password', $student) }}"
                                                      onsubmit="return confirm('Reset password to registration number?')">
                                                    @csrf
                                                    <button class="dropdown-item text-warning">
                                                        Reset Password
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
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
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">No students found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

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

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="create_account"
                                       value="1"
                                       id="create_account_student">
                                <label class="form-check-label" for="create_account_student">
                                    Create Student Portal Account
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                If portal account is created, username and default password will both be the registration number.
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

@can('student.create')
<div class="modal fade" id="importStudentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5>Import Students</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Upload CSV / Excel File</label>
                        <input type="file" name="import_file" class="form-control" required>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="create_accounts"
                               value="1"
                               id="create_accounts_import">
                        <label class="form-check-label" for="create_accounts_import">
                            Create portal accounts automatically
                        </label>
                    </div>

                    <div class="alert alert-info mt-3 mb-3">
                        Import file should use <strong>program name</strong> and <strong>level name/code</strong>, not IDs.<br>
                        Example columns:
                        <code>program, level, reg_no, first_name, second_name, last_name, gender, date_of_birth, phone_no, email</code>
                    </div>

                    <a href="{{ route('students.template.download') }}" class="btn btn-outline-secondary btn-sm">
                        Download Template
                    </a>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('check-all-students');
        const checkboxes = document.querySelectorAll('.student-checkbox');

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
            });
        }
    });
</script>
@endpush
@endsection