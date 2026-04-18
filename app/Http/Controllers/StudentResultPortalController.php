<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentResultPortalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $student = $user->student;

        abort_unless($student, 403, 'Student account is not linked.');
        abort_unless($user->can('student-portal.view') || $user->hasRole('Student'), 403);

        $results = ExamResult::query()
            ->with([
                'studentExamNumber',
                'studentLevelPlacement.programLevel',
                'examSession.academicYear',
                'gpaClassification',
            ])
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('student-portal.results.index', compact('student', 'results'));
    }

    public function show(ExamResult $examResult, Request $request): View
    {
        $user = $request->user();
        $student = $user->student;

        abort_unless($student, 403, 'Student account is not linked.');
        abort_unless($user->can('student-portal.view') || $user->hasRole('Student'), 403);
        abort_unless((int) $examResult->student_id === (int) $student->id, 403);
        abort_unless($examResult->is_published, 403);

        $examResult->load([
            'student',
            'studentExamNumber',
            'studentLevelPlacement.programLevel',
            'examSession.academicYear',
            'gpaClassification',
        ]);

        $courseResults = \App\Models\StudentCourseResult::query()
            ->with(['course.programLevel.program', 'grade'])
            ->where('student_id', $student->id)
            ->where('exam_session_id', $examResult->exam_session_id)
            ->orderBy('course_id')
            ->get();

        return view('student-portal.results.show', compact('student', 'examResult', 'courseResults'));
    }
}