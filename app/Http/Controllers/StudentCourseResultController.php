<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Course;
use App\Models\ExamSession;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentCourseResult;
use App\Models\StudentExamNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentCourseResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:result-entry.view')->only('index');
        $this->middleware('permission:result-entry.create')->only('store');
        $this->middleware('permission:result-entry.update')->only('update');
        $this->middleware('permission:result-entry.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $students = Student::orderBy('first_name')->orderBy('last_name')->get();
        $examSessions = ExamSession::with('academicYear')->orderByDesc('start_date')->get();
        $grades = Grade::orderByDesc('max_score')->orderByDesc('grade_point')->get();

        $courses = Course::with(['programLevel.program'])
            ->orderBy('name')
            ->get();

        $studentExamNumbers = StudentExamNumber::with(['student', 'academicYear'])
            ->orderByDesc('is_current')
            ->latest()
            ->get();

        $results = StudentCourseResult::query()
            ->with([
                'student.program',
                'student.programLevel',
                'studentExamNumber',
                'course.programLevel.program',
                'examSession.academicYear',
                'grade',
                'enteredBy',
                'approvedBy',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                        ->orWhere('comment_snapshot', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($sub) use ($search) {
                            $sub->where('reg_no', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('second_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('studentExamNumber', function ($sub) use ($search) {
                            $sub->where('exam_no', 'like', "%{$search}%");
                        })
                        ->orWhereHas('course', function ($sub) use ($search) {
                            $sub->where('code', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('examSession', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('grade', function ($sub) use ($search) {
                            $sub->where('grade_code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('student_id'), function ($query) use ($request) {
                $query->where('student_id', $request->student_id);
            })
            ->when($request->filled('exam_session_id'), function ($query) use ($request) {
                $query->where('exam_session_id', $request->exam_session_id);
            })
            ->when($request->filled('course_id'), function ($query) use ($request) {
                $query->where('course_id', $request->course_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('course-results.index', compact(
            'results',
            'students',
            'studentExamNumbers',
            'courses',
            'examSessions',
            'grades'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'student_exam_number_id' => ['required', 'exists:student_exam_numbers,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'grade_id' => ['required', 'exists:grades,id'],
            'raw_score' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,approved,rejected'],
        ]);

        $studentExamNumber = StudentExamNumber::findOrFail($validated['student_exam_number_id']);
        if ((int) $studentExamNumber->student_id !== (int) $validated['student_id']) {
            return back()->withInput()->with('error', 'Selected exam number does not belong to the selected student.');
        }

        $grade = Grade::findOrFail($validated['grade_id']);

        $data = [
            'student_id' => $validated['student_id'],
            'student_exam_number_id' => $validated['student_exam_number_id'],
            'course_id' => $validated['course_id'],
            'exam_session_id' => $validated['exam_session_id'],
            'grade_id' => $validated['grade_id'],
            'raw_score' => $validated['raw_score'] ?? null,
            'grade_point_snapshot' => $grade->grade_point,
            'comment_snapshot' => $grade->comment_label,
            'entered_by' => Auth::id(),
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'approved') {
            $data['approved_by'] = Auth::id();
            $data['approved_at'] = now();
        }

        StudentCourseResult::create($data);

        return redirect()
            ->route('course-results.index')
            ->with('success', 'Course result created successfully.');
    }

    public function update(Request $request, StudentCourseResult $courseResult): RedirectResponse
    {

    if ($courseResult->status === 'approved' && !Auth::user()->can('result-entry.approve')) {
    return redirect()
        ->route('course-results.index')
        ->with('error', 'Approved results cannot be edited directly.');
}
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'student_exam_number_id' => ['required', 'exists:student_exam_numbers,id'],
            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::unique('student_course_results')
                    ->where(function ($query) use ($request) {
                        return $query
                            ->where('student_id', $request->student_id)
                            ->where('exam_session_id', $request->exam_session_id);
                    })
                    ->ignore($courseResult->id),
            ],
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'grade_id' => ['required', 'exists:grades,id'],
            'raw_score' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,approved,rejected'],
        ]);

        $studentExamNumber = StudentExamNumber::findOrFail($validated['student_exam_number_id']);
        if ((int) $studentExamNumber->student_id !== (int) $validated['student_id']) {
            return back()->withInput()->with('error', 'Selected exam number does not belong to the selected student.');
        }

        $grade = Grade::findOrFail($validated['grade_id']);

        $data = [
            'student_id' => $validated['student_id'],
            'student_exam_number_id' => $validated['student_exam_number_id'],
            'course_id' => $validated['course_id'],
            'exam_session_id' => $validated['exam_session_id'],
            'grade_id' => $validated['grade_id'],
            'raw_score' => $validated['raw_score'] ?? null,
            'grade_point_snapshot' => $grade->grade_point,
            'comment_snapshot' => $grade->comment_label,
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'approved') {
            $data['approved_by'] = Auth::id();
            $data['approved_at'] = now();
        } else {
            $data['approved_by'] = null;
            $data['approved_at'] = null;
        }

        $courseResult->update($data);

        return redirect()
            ->route('course-results.index')
            ->with('success', 'Course result updated successfully.');
    }

    public function destroy(StudentCourseResult $courseResult): RedirectResponse
    {

    if ($courseResult->status === 'approved') {
    return redirect()
        ->route('course-results.index')
        ->with('error', 'Approved results cannot be deleted. Unapprove first.');
}
        $courseResult->delete();

        return redirect()
            ->route('course-results.index')
            ->with('success', 'Course result deleted successfully.');
    }
}