<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\AcademicYear;
use App\Models\ProgramLevel;
use App\Models\StudentLevelPlacement;
use App\Models\StudentProgramEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentLevelPlacementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student.view')->only('index');
        $this->middleware('permission:student.update')->only('store', 'update', 'destroy');
    }

    public function index(Request $request): View
    {
        $enrollments = StudentProgramEnrollment::with(['student', 'program'])
            ->orderByDesc('id')
            ->get();

        $programLevels = ProgramLevel::with('program')
            ->orderBy('program_id')
            ->orderBy('sort_order')
            ->orderBy('level_number')
            ->get();

        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $placements = StudentLevelPlacement::query()
            ->with([
                'enrollment.student.program',
                'enrollment.student.programLevel',
                'enrollment.program',
                'programLevel.program',
                'academicYear'
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('progression_status', 'like', "%{$search}%")
                        ->orWhere('placement_reason', 'like', "%{$search}%")
                        ->orWhereHas('enrollment.student', function ($sub) use ($search) {
                            $sub->where('reg_no', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('second_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('programLevel', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('student_program_enrollment_id'), function ($query) use ($request) {
                $query->where('student_program_enrollment_id', $request->student_program_enrollment_id);
            })
            ->when($request->filled('program_level_id'), function ($query) use ($request) {
                $query->where('program_level_id', $request->program_level_id);
            })
            ->when($request->filled('academic_year_id'), function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id);
            })
            ->when($request->filled('current'), function ($query) use ($request) {
                $query->where('is_current', $request->current);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('programs.student-level-placements', compact(
            'placements',
            'enrollments',
            'programLevels',
            'academicYears'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_program_enrollment_id' => ['required', 'exists:student_program_enrollments,id'],
            'program_level_id' => ['required', 'exists:program_levels,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['nullable', 'boolean'],
            'progression_status' => ['nullable', 'string', 'max:255'],
            'placement_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $enrollment = StudentProgramEnrollment::with('student')->findOrFail($validated['student_program_enrollment_id']);
        $level = ProgramLevel::findOrFail($validated['program_level_id']);

        if ((int) $enrollment->program_id !== (int) $level->program_id) {
            return back()
                ->withInput()
                ->with('error', 'Selected level does not belong to the enrollment program.');
        }

        if ($request->boolean('is_current', true)) {
            StudentLevelPlacement::where('student_program_enrollment_id', $enrollment->id)
                ->update(['is_current' => false]);
        }

        StudentLevelPlacement::create([
            'student_program_enrollment_id' => $validated['student_program_enrollment_id'],
            'program_level_id' => $validated['program_level_id'],
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_current' => $request->boolean('is_current', true),
            'progression_status' => isset($validated['progression_status']) ? trim($validated['progression_status']) : null,
            'placement_reason' => isset($validated['placement_reason']) ? trim($validated['placement_reason']) : null,
        ]);

        if ($request->boolean('is_current', true)) {
            $enrollment->student->update([
                'program_id' => $enrollment->program_id,
                'program_level_id' => $validated['program_level_id'],
            ]);
        }

        return redirect()
            ->route('student-level-placements.index')
            ->with('success', 'Student level placement created successfully.');
    }

    public function update(Request $request, StudentLevelPlacement $studentLevelPlacement): RedirectResponse
    {
        $validated = $request->validate([
            'student_program_enrollment_id' => ['required', 'exists:student_program_enrollments,id'],
            'program_level_id' => ['required', 'exists:program_levels,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_current' => ['nullable', 'boolean'],
            'progression_status' => ['nullable', 'string', 'max:255'],
            'placement_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $enrollment = StudentProgramEnrollment::with('student')->findOrFail($validated['student_program_enrollment_id']);
        $level = ProgramLevel::findOrFail($validated['program_level_id']);

        if ((int) $enrollment->program_id !== (int) $level->program_id) {
            return back()
                ->withInput()
                ->with('error', 'Selected level does not belong to the enrollment program.');
        }

        if ($request->boolean('is_current')) {
            StudentLevelPlacement::where('student_program_enrollment_id', $enrollment->id)
                ->where('id', '!=', $studentLevelPlacement->id)
                ->update(['is_current' => false]);
        }

        $studentLevelPlacement->update([
            'student_program_enrollment_id' => $validated['student_program_enrollment_id'],
            'program_level_id' => $validated['program_level_id'],
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_current' => $request->boolean('is_current'),
            'progression_status' => isset($validated['progression_status']) ? trim($validated['progression_status']) : null,
            'placement_reason' => isset($validated['placement_reason']) ? trim($validated['placement_reason']) : null,
        ]);

        if ($request->boolean('is_current')) {
            $enrollment->student->update([
                'program_id' => $enrollment->program_id,
                'program_level_id' => $validated['program_level_id'],
            ]);
        }

        return redirect()
            ->route('student-level-placements.index')
            ->with('success', 'Student level placement updated successfully.');
    }

    public function destroy(StudentLevelPlacement $studentLevelPlacement): RedirectResponse
    {
        $studentLevelPlacement->delete();

        return redirect()
            ->route('student-level-placements.index')
            ->with('success', 'Student level placement deleted successfully.');
    }
}