<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Program;
use App\Models\ProgramLevel;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student.view')->only('index');
        $this->middleware('permission:student.create')->only('store');
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
            ->with(['program', 'programLevel'])
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
        ]);

        $this->ensureLevelBelongsToProgram(
            (int) $validated['program_id'],
            (int) $validated['program_level_id']
        );

        Student::create([
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
        ]);

        $this->ensureLevelBelongsToProgram(
            (int) $validated['program_id'],
            (int) $validated['program_level_id']
        );

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

        return redirect()
            ->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    private function ensureLevelBelongsToProgram(int $programId, int $programLevelId): void
    {
        $valid = ProgramLevel::where('id', $programLevelId)
            ->where('program_id', $programId)
            ->exists();

        abort_unless($valid, 422, 'Selected level does not belong to the selected program.');
    }
}