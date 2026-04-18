<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Course;
use App\Models\ExamResult;
use App\Models\ExamSession;
use App\Models\Program;
use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\StudentCourseResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('Student')) {
            return redirect()->route('student.portal.dashboard');
        }

        $stats = [
            'users' => User::count(),
            'students' => Student::count(),
            'programs' => Program::count(),
            'courses' => Course::count(),
            'exam_sessions' => ExamSession::count(),
            'published_exam_results' => ExamResult::where('is_published', true)->count(),
        ];

        $studentsByProgram = Program::query()
            ->withCount('students')
            ->orderByDesc('students_count')
            ->get(['id', 'name']);

        $studentsByLevel = ProgramLevel::query()
            ->withCount('students')
            ->orderBy('level_number')
            ->get(['id', 'name']);

        $courseResultsStatus = StudentCourseResult::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $examResultsPublication = ExamResult::query()
            ->selectRaw('is_published, COUNT(*) as total')
            ->groupBy('is_published')
            ->pluck('total', 'is_published');

        $recentStudents = Student::with(['program', 'programLevel'])
            ->latest()
            ->take(5)
            ->get();

        $recentCourseResults = StudentCourseResult::with([
            'student',
            'course',
            'examSession',
            'grade'
        ])
        ->latest()
        ->take(5)
        ->get();

        $recentExamResults = ExamResult::with([
            'student',
            'examSession',
            'gpaClassification'
        ])
        ->latest()
        ->take(5)
        ->get();

        $chartData = [
            'studentsByProgramLabels' => $studentsByProgram->pluck('name')->values(),
            'studentsByProgramSeries' => $studentsByProgram->pluck('students_count')->values(),

            'studentsByLevelLabels' => $studentsByLevel->pluck('name')->values(),
            'studentsByLevelSeries' => $studentsByLevel->pluck('students_count')->values(),

            'courseResultsStatusLabels' => $courseResultsStatus->keys()->map(fn ($item) => ucfirst($item))->values(),
            'courseResultsStatusSeries' => $courseResultsStatus->values(),

            'examResultsPublicationLabels' => collect([
                'Draft',
                'Published',
            ]),
            'examResultsPublicationSeries' => collect([
                (int) ($examResultsPublication[0] ?? 0),
                (int) ($examResultsPublication[1] ?? 0),
            ]),
        ];

        return view('dashboard', compact(
            'stats',
            'recentStudents',
            'recentCourseResults',
            'recentExamResults',
            'chartData'
        ));
    }
}