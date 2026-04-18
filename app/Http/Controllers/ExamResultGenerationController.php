<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\ExamResultGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ExamResultGenerationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:result-summary.generate')->only('generate');
    }

    public function generate(Request $request, ExamResultGenerationService $service): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
        ]);

        try {
            $service->generate(
                (int) $validated['student_id'],
                (int) $validated['exam_session_id']
            );

            return redirect()
                ->route('exam-results.index')
                ->with('success', 'Exam result generated successfully.');
        } catch (RuntimeException $e) {
            return redirect()
                ->route('exam-results.index')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()
                ->route('exam-results.index')
                ->with('error', 'Exam result generation failed.');
        }
    }
}