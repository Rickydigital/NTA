<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:grade.view')->only('index');
        $this->middleware('permission:grade.create')->only('store');
        $this->middleware('permission:grade.update')->only('update');
        $this->middleware('permission:grade.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $grades = Grade::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('grade_code', 'like', "%{$search}%")
                        ->orWhere('comment_label', 'like', "%{$search}%")
                        ->orWhere('result_status', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('max_score')
            ->orderByDesc('grade_point')
            ->paginate(10)
            ->withQueryString();

        return view('grades.index', compact('grades'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'grade_code' => ['required', 'string', 'max:50', 'unique:grades,grade_code'],
            'grade_point' => ['required', 'numeric', 'min:0'],
            'min_score' => ['nullable', 'numeric', 'min:0'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'comment_label' => ['nullable', 'string', 'max:255'],
            'result_status' => ['nullable', 'string', 'max:255'],
            'affects_gpa' => ['nullable', 'boolean'],
            'is_pass_grade' => ['nullable', 'boolean'],
        ]);

        if (
            isset($validated['min_score'], $validated['max_score']) &&
            (float) $validated['min_score'] > (float) $validated['max_score']
        ) {
            return back()
                ->withInput()
                ->with('error', 'Minimum score cannot be greater than maximum score.');
        }

        Grade::create([
            'grade_code' => strtoupper(trim($validated['grade_code'])),
            'grade_point' => $validated['grade_point'],
            'min_score' => $validated['min_score'] ?? null,
            'max_score' => $validated['max_score'] ?? null,
            'comment_label' => isset($validated['comment_label']) ? trim($validated['comment_label']) : null,
            'result_status' => isset($validated['result_status']) ? trim($validated['result_status']) : null,
            'affects_gpa' => $request->boolean('affects_gpa', true),
            'is_pass_grade' => $request->boolean('is_pass_grade'),
        ]);

        return redirect()
            ->route('grades.index')
            ->with('success', 'Grade created successfully.');
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $validated = $request->validate([
            'grade_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('grades', 'grade_code')->ignore($grade->id),
            ],
            'grade_point' => ['required', 'numeric', 'min:0'],
            'min_score' => ['nullable', 'numeric', 'min:0'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'comment_label' => ['nullable', 'string', 'max:255'],
            'result_status' => ['nullable', 'string', 'max:255'],
            'affects_gpa' => ['nullable', 'boolean'],
            'is_pass_grade' => ['nullable', 'boolean'],
        ]);

        if (
            isset($validated['min_score'], $validated['max_score']) &&
            (float) $validated['min_score'] > (float) $validated['max_score']
        ) {
            return back()
                ->withInput()
                ->with('error', 'Minimum score cannot be greater than maximum score.');
        }

        $grade->update([
            'grade_code' => strtoupper(trim($validated['grade_code'])),
            'grade_point' => $validated['grade_point'],
            'min_score' => $validated['min_score'] ?? null,
            'max_score' => $validated['max_score'] ?? null,
            'comment_label' => isset($validated['comment_label']) ? trim($validated['comment_label']) : null,
            'result_status' => isset($validated['result_status']) ? trim($validated['result_status']) : null,
            'affects_gpa' => $request->boolean('affects_gpa'),
            'is_pass_grade' => $request->boolean('is_pass_grade'),
        ]);

        return redirect()
            ->route('grades.index')
            ->with('success', 'Grade updated successfully.');
    }

    public function destroy(Grade $grade): RedirectResponse
    {
        $grade->delete();

        return redirect()
            ->route('grades.index')
            ->with('success', 'Grade deleted successfully.');
    }
}