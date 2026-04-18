<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $student = $user->student;

        abort_unless($student !== null, 403, 'Student account is not linked.');
        abort_unless($user->can('student-portal.view') || $user->hasRole('Student'), 403);

        $publishedResultsCount = ExamResult::query()
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->count();

        $latestResult = ExamResult::query()
            ->with([
                'studentExamNumber',
                'studentLevelPlacement.programLevel',
                'examSession.academicYear',
                'gpaClassification',
            ])
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->latest('generated_at')
            ->latest('id')
            ->first();

        return view('student-portal.dashboard', compact(
            'student',
            'publishedResultsCount',
            'latestResult'
        ));
    }
}