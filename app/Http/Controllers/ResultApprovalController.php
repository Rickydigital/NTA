<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\StudentCourseResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ResultApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:result-entry.approve')->only('approve', 'unapprove');
    }

    public function approve(StudentCourseResult $courseResult): RedirectResponse
    {
        $courseResult->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('course-results.index')
            ->with('success', 'Course result approved successfully.');
    }

    public function unapprove(StudentCourseResult $courseResult): RedirectResponse
    {
        $courseResult->update([
            'status' => 'draft',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()
            ->route('course-results.index')
            ->with('success', 'Course result moved back to draft successfully.');
    }
}