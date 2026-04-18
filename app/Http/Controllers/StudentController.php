<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Imports\StudentsImport;
use App\Models\Program;
use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Exports\StudentsTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student.view')->only('index');
        $this->middleware('permission:student.create')->only('store', 'import');
        $this->middleware('permission:student.update')->only('update');
        $this->middleware('permission:student.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $programs = Program::orderBy('name')->get();

        $programLevels = ProgramLevel::with('program')
            ->orderBy('program_id')
            ->orderBy('sort_order')
            ->orderBy('level_number')
            ->get();

        $students = Student::query()
            ->with(['program', 'programLevel', 'user'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('reg_no', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('second_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone_no', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('program', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('programLevel', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('program_id'), function ($query) use ($request) {
                $query->where('program_id', $request->program_id);
            })
            ->when($request->filled('program_level_id'), function ($query) use ($request) {
                $query->where('program_level_id', $request->program_level_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('students.index', compact('students', 'programs', 'programLevels'));
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(
            new StudentsTemplateExport(),
            'students-import-template.xlsx'
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'program_level_id' => ['required', 'exists:program_levels,id'],
            'reg_no' => ['required', 'string', 'max:100', 'unique:students,reg_no'],
            'first_name' => ['required', 'string', 'max:255'],
            'second_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date'],
            'phone_no' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'create_account' => ['nullable', 'boolean'],
        ]);

        $this->ensureLevelBelongsToProgram(
            (int) $validated['program_id'],
            (int) $validated['program_level_id']
        );

        DB::transaction(function () use ($validated, $request) {
            $student = Student::create([
                'program_id' => $validated['program_id'],
                'program_level_id' => $validated['program_level_id'],
                'reg_no' => strtoupper(trim($validated['reg_no'])),
                'first_name' => trim($validated['first_name']),
                'second_name' => isset($validated['second_name']) ? trim($validated['second_name']) : null,
                'last_name' => trim($validated['last_name']),
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'phone_no' => isset($validated['phone_no']) ? trim($validated['phone_no']) : null,
                'email' => isset($validated['email']) ? trim($validated['email']) : null,
            ]);

            if ($request->boolean('create_account')) {
                $this->createOrUpdateStudentUser($student);
            }
        });

        return redirect()
            ->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'program_level_id' => ['required', 'exists:program_levels,id'],
            'reg_no' => [
                'required',
                'string',
                'max:100',
                Rule::unique('students', 'reg_no')->ignore($student->id),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'second_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date'],
            'phone_no' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'create_account' => ['nullable', 'boolean'],
            'reset_password_to_reg_no' => ['nullable', 'boolean'],
        ]);

        $this->ensureLevelBelongsToProgram(
            (int) $validated['program_id'],
            (int) $validated['program_level_id']
        );

        DB::transaction(function () use ($validated, $request, $student) {
            $student->update([
                'program_id' => $validated['program_id'],
                'program_level_id' => $validated['program_level_id'],
                'reg_no' => strtoupper(trim($validated['reg_no'])),
                'first_name' => trim($validated['first_name']),
                'second_name' => isset($validated['second_name']) ? trim($validated['second_name']) : null,
                'last_name' => trim($validated['last_name']),
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'phone_no' => isset($validated['phone_no']) ? trim($validated['phone_no']) : null,
                'email' => isset($validated['email']) ? trim($validated['email']) : null,
            ]);

            if ($request->boolean('create_account')) {
                $this->createOrUpdateStudentUser($student, $request->boolean('reset_password_to_reg_no'));
            }
        });

        return redirect()
            ->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        DB::transaction(function () use ($student) {
            if ($student->user) {
                $student->user->delete();
            }

            $student->delete();
        });

        return redirect()
            ->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }
    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
            'create_accounts' => ['nullable', 'boolean'],
        ]);

        try {
            Excel::import(
                new StudentsImport($request->boolean('create_accounts')),
                $validated['import_file']
            );

            return redirect()
                ->route('students.index')
                ->with('success', 'Students imported successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('students.index')
                ->with('error', 'Student import failed: ' . $e->getMessage());
        }
    }
    private function ensureLevelBelongsToProgram(int $programId, int $programLevelId): void
    {
        $valid = ProgramLevel::where('id', $programLevelId)
            ->where('program_id', $programId)
            ->exists();

        abort_unless($valid, 422, 'Selected level does not belong to the selected program.');
    }

    private function createOrUpdateStudentUser(Student $student, bool $resetPassword = false): User
    {
        $username = strtoupper(trim($student->reg_no));
        $email = $student->email ? trim($student->email) : null;

        $user = User::firstOrNew(['student_id' => $student->id]);

        $user->first_name = $student->first_name;
        $user->second_name = $student->second_name;
        $user->last_name = $student->last_name;
        $user->gender = $student->gender;
        $user->phone_no = $student->phone_no;
        $user->username = $username;
        $user->email = $email;
        $user->is_active = true;

        if (!$user->exists || $resetPassword) {
            $user->password = Hash::make($username);
        }

        $user->save();
        $user->syncRoles(['Student']);

        return $user;
    }
}
