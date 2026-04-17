<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Student;
use App\Models\StudentProgramEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentProgramEnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student.view')->only('index');
        $this->middleware('permission:student.update')->only('store', 'update', 'destroy');
    }

    public function index(Request $request): View
    {
        $students = Student::orderBy('first_name')->orderBy('last_name')->get();
        $programs = Program::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $enrollments = StudentProgramEnrollment::query()
            ->with(['student.program', 'student.programLevel', 'program', 'intakeAcademicYear'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($sub) use ($search) {
                            $sub->where('reg_no', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('second_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('program', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('intakeAcademicYear', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('student_id'), function ($query) use ($request) {
                $query->where('student_id', $request->student_id);
            })
            ->when($request->filled('program_id'), function ($query) use ($request) {
                $query->where('program_id', $request->program_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('programs.student-program-enrollments', compact(
            'enrollments',
            'students',
            'programs',
            'academicYears'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'intake_academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'enrollment_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:enrollment_date'],
            'status' => ['required', 'in:active,completed,deferred,discontinued'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        StudentProgramEnrollment::create($validated);

        if ($validated['status'] === 'active') {
            $student->update([
                'program_id' => $validated['program_id'],
            ]);
        }

        return redirect()
            ->route('student-program-enrollments.index')
            ->with('success', 'Student program enrollment created successfully.');
    }

    public function update(Request $request, StudentProgramEnrollment $studentProgramEnrollment): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'intake_academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'enrollment_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:enrollment_date'],
            'status' => ['required', 'in:active,completed,deferred,discontinued'],
        ]);

        $studentProgramEnrollment->update($validated);

        if ($validated['status'] === 'active') {
            $studentProgramEnrollment->student->update([
                'program_id' => $validated['program_id'],
            ]);
        }

        return redirect()
            ->route('student-program-enrollments.index')
            ->with('success', 'Student program enrollment updated successfully.');
    }

    public function destroy(StudentProgramEnrollment $studentProgramEnrollment): RedirectResponse
    {
        $studentProgramEnrollment->delete();

        return redirect()
            ->route('student-program-enrollments.index')
            ->with('success', 'Student program enrollment deleted successfully.');
    }
}