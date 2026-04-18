<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\ExamResult;
use App\Models\ExamSession;
use App\Models\GpaClassification;
use App\Models\Student;
use App\Models\StudentExamNumber;
use App\Models\StudentLevelPlacement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExamResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:result-summary.view')->only('index');
        $this->middleware('permission:result-summary.generate')->only('store', 'update');
        $this->middleware('permission:result-summary.publish')->only('destroy');
    }

    public function index(Request $request): View
    {
        $students = Student::orderBy('first_name')->orderBy('last_name')->get();
        $examSessions = ExamSession::with('academicYear')->orderByDesc('start_date')->get();
        $gpaClassifications = GpaClassification::orderBy('priority_order')->get();
        $studentExamNumbers = StudentExamNumber::with('student')->orderByDesc('is_current')->latest()->get();
        $studentLevelPlacements = StudentLevelPlacement::with(['enrollment.student', 'programLevel'])->latest()->get();

        $examResults = ExamResult::query()
            ->with([
                'student.program',
                'student.programLevel',
                'studentExamNumber',
                'studentLevelPlacement.programLevel',
                'examSession.academicYear',
                'gpaClassification',
                'publishedBy',
                'generatedBy',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('final_comment', 'like', "%{$search}%")
                        ->orWhere('progression_decision', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($sub) use ($search) {
                            $sub->where('reg_no', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('second_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('studentExamNumber', function ($sub) use ($search) {
                            $sub->where('exam_no', 'like', "%{$search}%");
                        })
                        ->orWhereHas('examSession', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('gpaClassification', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('classification_code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('student_id'), function ($query) use ($request) {
                $query->where('student_id', $request->student_id);
            })
            ->when($request->filled('exam_session_id'), function ($query) use ($request) {
                $query->where('exam_session_id', $request->exam_session_id);
            })
            ->when($request->filled('published'), function ($query) use ($request) {
                $query->where('is_published', $request->published);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('exam.results', compact(
            'examResults',
            'students',
            'examSessions',
            'gpaClassifications',
            'studentExamNumbers',
            'studentLevelPlacements'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'student_exam_number_id' => ['required', 'exists:student_exam_numbers,id'],
            'student_level_placement_id' => ['required', 'exists:student_level_placements,id'],
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
            'gpa_classification_id' => ['nullable', 'exists:gpa_classifications,id'],
            'total_courses' => ['required', 'integer', 'min:0'],
            'total_grade_points' => ['required', 'numeric', 'min:0'],
            'gpa' => ['required', 'numeric', 'min:0'],
            'final_comment' => ['nullable', 'string', 'max:255'],
            'progression_decision' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data = [
            'student_id' => $validated['student_id'],
            'student_exam_number_id' => $validated['student_exam_number_id'],
            'student_level_placement_id' => $validated['student_level_placement_id'],
            'exam_session_id' => $validated['exam_session_id'],
            'gpa_classification_id' => $validated['gpa_classification_id'] ?? null,
            'total_courses' => $validated['total_courses'],
            'total_grade_points' => $validated['total_grade_points'],
            'gpa' => $validated['gpa'],
            'final_comment' => $validated['final_comment'] ?? null,
            'progression_decision' => $validated['progression_decision'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'generated_at' => now(),
            'generated_by' => Auth::id(),
        ];

        if ($request->boolean('is_published')) {
            $data['published_at'] = now();
            $data['published_by'] = Auth::id();
        }

        ExamResult::create($data);

        return redirect()
            ->route('exam-results.index')
            ->with('success', 'Exam result created successfully.');
    }

    public function update(Request $request, ExamResult $examResult): RedirectResponse
    {

        if ($examResult->is_published) {
            return redirect()
                ->route('exam-results.index')
                ->with('error', 'Published exam results must be unpublished before editing.');
        }
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'student_exam_number_id' => ['required', 'exists:student_exam_numbers,id'],
            'student_level_placement_id' => ['required', 'exists:student_level_placements,id'],
            'exam_session_id' => [
                'required',
                'exists:exam_sessions,id',
                Rule::unique('exam_results')
                    ->where(function ($query) use ($request) {
                        return $query->where('student_id', $request->student_id);
                    })
                    ->ignore($examResult->id),
            ],
            'gpa_classification_id' => ['nullable', 'exists:gpa_classifications,id'],
            'total_courses' => ['required', 'integer', 'min:0'],
            'total_grade_points' => ['required', 'numeric', 'min:0'],
            'gpa' => ['required', 'numeric', 'min:0'],
            'final_comment' => ['nullable', 'string', 'max:255'],
            'progression_decision' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data = [
            'student_id' => $validated['student_id'],
            'student_exam_number_id' => $validated['student_exam_number_id'],
            'student_level_placement_id' => $validated['student_level_placement_id'],
            'exam_session_id' => $validated['exam_session_id'],
            'gpa_classification_id' => $validated['gpa_classification_id'] ?? null,
            'total_courses' => $validated['total_courses'],
            'total_grade_points' => $validated['total_grade_points'],
            'gpa' => $validated['gpa'],
            'final_comment' => $validated['final_comment'] ?? null,
            'progression_decision' => $validated['progression_decision'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'generated_at' => $examResult->generated_at ?? now(),
            'generated_by' => $examResult->generated_by ?? Auth::id(),
        ];

        if ($request->boolean('is_published')) {
            $data['published_at'] = now();
            $data['published_by'] = Auth::id();
        } else {
            $data['published_at'] = null;
            $data['published_by'] = null;
        }

        $examResult->update($data);

        return redirect()
            ->route('exam-results.index')
            ->with('success', 'Exam result updated successfully.');
    }

    public function destroy(ExamResult $examResult): RedirectResponse
    {
        if ($examResult->is_published) {
            return redirect()
                ->route('exam-results.index')
                ->with('error', 'Published exam results cannot be deleted. Unpublish first.');
        }

        $examResult->delete();

        return redirect()
            ->route('exam-results.index')
            ->with('success', 'Exam result deleted successfully.');
    }
}
