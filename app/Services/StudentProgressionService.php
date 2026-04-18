<?php

namespace App\Services;

use App\Models\ExamResult;
use App\Models\ProgressionRule;
use App\Models\StudentLevelPlacement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StudentProgressionService
{
    public function execute(ExamResult $examResult): array
    {
        return DB::transaction(function () use ($examResult) {
            $examResult->loadMissing([
                'student',
                'studentLevelPlacement.enrollment',
                'studentLevelPlacement.programLevel',
                'gpaClassification',
            ]);

            $student = $examResult->student;
            $currentPlacement = $examResult->studentLevelPlacement;
            $enrollment = $currentPlacement?->enrollment;
            $currentLevel = $currentPlacement?->programLevel;

            if (!$student || !$currentPlacement || !$enrollment || !$currentLevel) {
                throw new RuntimeException('Exam result is missing required student placement data.');
            }

            if (!$currentPlacement->is_current) {
                throw new RuntimeException('This exam result is linked to a non-current placement.');
            }

            $programId = (int) $enrollment->program_id;

            $failedCourses = $this->countFailedCourses($examResult->student_id, $examResult->exam_session_id);

            $rules = ProgressionRule::query()
                ->where('program_id', $programId)
                ->where('from_program_level_id', $currentLevel->id)
                ->orderBy('id')
                ->get();

            if ($rules->isEmpty()) {
                throw new RuntimeException('No progression rule found for this program and level.');
            }

            $selectedRule = null;

            foreach ($rules as $rule) {
                if ($rule->min_gpa_required !== null && (float) $examResult->gpa < (float) $rule->min_gpa_required) {
                    continue;
                }

                if ($failedCourses > (int) $rule->max_failed_courses_allowed) {
                    continue;
                }

                if ($rule->requires_manual_approval) {
                    $selectedRule = $rule;
                    break;
                }

                $selectedRule = $rule;
                break;
            }

            if (!$selectedRule) {
                throw new RuntimeException('No matching progression rule satisfied the current result.');
            }

            $decision = $selectedRule->decision;

            if ($decision === 'manual_review') {
                $examResult->update([
                    'progression_decision' => 'manual_review',
                ]);

                $currentPlacement->update([
                    'progression_status' => 'manual_review',
                ]);

                return [
                    'status' => 'manual_review',
                    'message' => 'Result requires manual review before progression.',
                ];
            }

            if ($decision === 'retained') {
                $examResult->update([
                    'progression_decision' => 'retained',
                ]);

                $currentPlacement->update([
                    'progression_status' => 'retained',
                ]);

                return [
                    'status' => 'retained',
                    'message' => 'Student has been retained in the current level.',
                ];
            }

            if ($decision === 'disco') {
                $examResult->update([
                    'progression_decision' => 'disco',
                ]);

                $currentPlacement->update([
                    'is_current' => false,
                    'end_date' => $currentPlacement->end_date ?? now()->toDateString(),
                    'progression_status' => 'disco',
                ]);

                $enrollment->update([
                    'status' => 'discontinued',
                    'completion_date' => $enrollment->completion_date ?? now()->toDateString(),
                ]);

                return [
                    'status' => 'disco',
                    'message' => 'Student has been marked as discontinued.',
                ];
            }

            if ($decision === 'completed') {
                $examResult->update([
                    'progression_decision' => 'completed',
                ]);

                $currentPlacement->update([
                    'is_current' => false,
                    'end_date' => $currentPlacement->end_date ?? now()->toDateString(),
                    'progression_status' => 'completed',
                ]);

                $enrollment->update([
                    'status' => 'completed',
                    'completion_date' => $enrollment->completion_date ?? now()->toDateString(),
                ]);

                return [
                    'status' => 'completed',
                    'message' => 'Student has completed the final level.',
                ];
            }

            if ($decision === 'proceed') {
                if (!$selectedRule->to_program_level_id) {
                    throw new RuntimeException('Proceed rule has no target level.');
                }

                $currentPlacement->update([
                    'is_current' => false,
                    'end_date' => $currentPlacement->end_date ?? now()->toDateString(),
                    'progression_status' => 'proceed',
                ]);

                $newPlacement = StudentLevelPlacement::create([
                    'student_program_enrollment_id' => $enrollment->id,
                    'program_level_id' => $selectedRule->to_program_level_id,
                    'academic_year_id' => $currentPlacement->academic_year_id,
                    'start_date' => now()->toDateString(),
                    'end_date' => null,
                    'is_current' => true,
                    'progression_status' => 'proceed',
                    'placement_reason' => 'auto_progression',
                ]);

                $student->update([
                    'program_id' => $enrollment->program_id,
                    'program_level_id' => $selectedRule->to_program_level_id,
                ]);

                $examResult->update([
                    'progression_decision' => 'proceed',
                ]);

                return [
                    'status' => 'proceed',
                    'message' => 'Student progressed successfully to the next level.',
                    'new_placement_id' => $newPlacement->id,
                ];
            }

            throw new RuntimeException('Unsupported progression decision.');
        });
    }

    protected function countFailedCourses(int $studentId, int $examSessionId): int
    {
        return \App\Models\StudentCourseResult::query()
            ->where('student_id', $studentId)
            ->where('exam_session_id', $examSessionId)
            ->whereHas('grade', function ($query) {
                $query->where('is_pass_grade', false);
            })
            ->count();
    }
}