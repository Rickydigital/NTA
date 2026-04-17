<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\AcademicYear;
use App\Models\ExamSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExamSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:exam-session.view')->only('index');
        $this->middleware('permission:exam-session.create')->only('store');
        $this->middleware('permission:exam-session.update')->only('update');
        $this->middleware('permission:exam-session.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $examSessions = ExamSession::query()
            ->with('academicYear')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('session_type', 'like', "%{$search}%")
                        ->orWhereHas('academicYear', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('academic_year_id'), function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id);
            })
            ->orderByDesc('start_date')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('exam-sessions.index', compact('examSessions', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('exam_sessions', 'name')->where(function ($query) use ($request) {
                    return $query->where('academic_year_id', $request->academic_year_id);
                }),
            ],
            'session_type' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_result_entry_open' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        ExamSession::create([
            'academic_year_id' => $validated['academic_year_id'],
            'name' => trim($validated['name']),
            'session_type' => isset($validated['session_type']) ? trim($validated['session_type']) : null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_result_entry_open' => $request->boolean('is_result_entry_open'),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('exam-sessions.index')
            ->with('success', 'Exam session created successfully.');
    }

    public function update(Request $request, ExamSession $examSession): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('exam_sessions', 'name')
                    ->where(function ($query) use ($request) {
                        return $query->where('academic_year_id', $request->academic_year_id);
                    })
                    ->ignore($examSession->id),
            ],
            'session_type' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_result_entry_open' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $examSession->update([
            'academic_year_id' => $validated['academic_year_id'],
            'name' => trim($validated['name']),
            'session_type' => isset($validated['session_type']) ? trim($validated['session_type']) : null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_result_entry_open' => $request->boolean('is_result_entry_open'),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('exam-sessions.index')
            ->with('success', 'Exam session updated successfully.');
    }

    public function destroy(ExamSession $examSession): RedirectResponse
    {
        $examSession->delete();

        return redirect()
            ->route('exam-sessions.index')
            ->with('success', 'Exam session deleted successfully.');
    }
}