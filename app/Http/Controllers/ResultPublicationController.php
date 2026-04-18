<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\ExamResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ResultPublicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:result-summary.publish')->only('publish', 'unpublish');
    }

    public function publish(ExamResult $examResult): RedirectResponse
    {
        $examResult->update([
            'is_published' => true,
            'published_at' => now(),
            'published_by' => Auth::id(),
        ]);

        return redirect()
            ->route('exam-results.index')
            ->with('success', 'Exam result published successfully.');
    }

    public function unpublish(ExamResult $examResult): RedirectResponse
    {
        $examResult->update([
            'is_published' => false,
            'published_at' => null,
            'published_by' => null,
        ]);

        return redirect()
            ->route('exam-results.index')
            ->with('success', 'Exam result unpublished successfully.');
    }
}