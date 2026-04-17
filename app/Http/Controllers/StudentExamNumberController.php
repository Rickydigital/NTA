<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentExamNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentExamNumberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:exam-number.view')->only('index');
        $this->middleware('permission:exam-number.create')->only('store');
        $this->middleware('permission:exam-number.update')->only('update');
        $this->middleware('permission:exam-number.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $students = Student::orderBy('first_name')->orderBy('last_name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $studentExamNumbers = StudentExamNumber::query()
            ->with(['student.program', 'student.programLevel', 'academicYear'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('exam_no', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($sub) use ($search) {
                            $sub->where('reg_no', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('second_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('academicYear', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('student_id'), function ($query) use ($request) {
                $query->where('student_id', $request->student_id);
            })
            ->when($request->filled('academic_year_id'), function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id);
            })
            ->orderByDesc('is_current')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('student-exam-numbers.index', compact('studentExamNumbers', 'students', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'exam_no' => ['required', 'string', 'max:100', 'unique:student_exam_numbers,exam_no'],
            'issued_at' => ['nullable', 'date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_current')) {
            StudentExamNumber::where('student_id', $validated['student_id'])
                ->update(['is_current' => false]);
        }

        StudentExamNumber::create([
            'student_id' => $validated['student_id'],
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'exam_no' => strtoupper(trim($validated['exam_no'])),
            'issued_at' => $validated['issued_at'] ?? null,
            'is_current' => $request->boolean('is_current', true),
        ]);

        return redirect()
            ->route('student-exam-numbers.index')
            ->with('success', 'Student exam number created successfully.');
    }

    public function update(Request $request, StudentExamNumber $studentExamNumber): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'exam_no' => [
                'required',
                'string',
                'max:100',
                Rule::unique('student_exam_numbers', 'exam_no')->ignore($studentExamNumber->id),
            ],
            'issued_at' => ['nullable', 'date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_current')) {
            StudentExamNumber::where('student_id', $validated['student_id'])
                ->where('id', '!=', $studentExamNumber->id)
                ->update(['is_current' => false]);
        }

        $studentExamNumber->update([
            'student_id' => $validated['student_id'],
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'exam_no' => strtoupper(trim($validated['exam_no'])),
            'issued_at' => $validated['issued_at'] ?? null,
            'is_current' => $request->boolean('is_current'),
        ]);

        return redirect()
            ->route('student-exam-numbers.index')
            ->with('success', 'Student exam number updated successfully.');
    }

    public function destroy(StudentExamNumber $studentExamNumber): RedirectResponse
    {
        $studentExamNumber->delete();

        return redirect()
            ->route('student-exam-numbers.index')
            ->with('success', 'Student exam number deleted successfully.');
    }
}