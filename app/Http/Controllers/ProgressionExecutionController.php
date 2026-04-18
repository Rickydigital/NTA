<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\ExamResult;
use App\Services\StudentProgressionService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class ProgressionExecutionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:progression.execute')->only('execute');
    }

    public function execute(ExamResult $examResult, StudentProgressionService $service): RedirectResponse
    {
        try {
            $result = $service->execute($examResult);

            return redirect()
                ->route('exam-results.index')
                ->with('success', $result['message']);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('exam-results.index')
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()
                ->route('exam-results.index')
                ->with('error', 'Progression execution failed.');
        }
    }
}