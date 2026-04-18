<?php

namespace App\Services;

use App\Models\ExamResult;
use App\Models\GpaClassification;
use App\Models\StudentCourseResult;
use App\Models\StudentLevelPlacement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ExamResultGenerationService
{
    public function generate(int $studentId, int $examSessionId): ExamResult
    {
        return DB::transaction(function () use ($studentId, $examSessionId) {
            $courseResults = StudentCourseResult::query()
                ->with(['grade', 'studentExamNumber'])
                ->where('student_id', $studentId)
                ->where('exam_session_id', $examSessionId)
                ->where('status', 'approved')
                ->get();

            if ($courseResults->isEmpty()) {
                throw new RuntimeException('No approved course results found for this student and exam session.');
            }

            $studentExamNumberId = $courseResults->first()->student_exam_number_id;

            $studentLevelPlacement = StudentLevelPlacement::query()
                ->whereHas('enrollment', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->where('is_current', true)
                ->latest()
                ->first();

            if (!$studentLevelPlacement) {
                throw new RuntimeException('No current student level placement found.');
            }

            $totalCourses = $courseResults->count();
            $totalGradePoints = (float) $courseResults->sum(function ($row) {
                return (float) ($row->grade_point_snapshot ?? 0);
            });

            $gpa = $totalCourses > 0 ? round($totalGradePoints / $totalCourses, 2) : 0;

            $classification = GpaClassification::query()
                ->where('min_gpa', '<=', $gpa)
                ->where('max_gpa', '>=', $gpa)
                ->orderBy('priority_order')
                ->first();

            $finalComment = $classification?->final_comment;
            $progressionDecision = $classification?->progression_action;

            $examResult = ExamResult::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'exam_session_id' => $examSessionId,
                ],
                [
                    'student_exam_number_id' => $studentExamNumberId,
                    'student_level_placement_id' => $studentLevelPlacement->id,
                    'gpa_classification_id' => $classification?->id,
                    'total_courses' => $totalCourses,
                    'total_grade_points' => $totalGradePoints,
                    'gpa' => $gpa,
                    'final_comment' => $finalComment,
                    'progression_decision' => $progressionDecision,
                    'generated_at' => now(),
                    'generated_by' => Auth::id(),
                ]
            );

            return $examResult;
        });
    }
}