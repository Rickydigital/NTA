<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\ProgramLevel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:course.view')->only('index');
        $this->middleware('permission:course.create')->only('store');
        $this->middleware('permission:course.update')->only('update');
        $this->middleware('permission:course.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $programs = Program::orderBy('name')->get();

        $programLevels = ProgramLevel::with('program')
            ->orderBy('program_id')
            ->orderBy('sort_order')
            ->orderBy('level_number')
            ->get();

        $courses = Course::query()
            ->with(['programLevel.program'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('programLevel', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%")
                                ->orWhereHas('program', function ($programQuery) use ($search) {
                                    $programQuery->where('name', 'like', "%{$search}%")
                                        ->orWhere('code', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($request->filled('program_id'), function ($query) use ($request) {
                $query->whereHas('programLevel', function ($sub) use ($request) {
                    $sub->where('program_id', $request->program_id);
                });
            })
            ->when($request->filled('program_level_id'), function ($query) use ($request) {
                $query->where('program_level_id', $request->program_level_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('courses.index', compact('courses', 'programs', 'programLevels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_level_id' => ['required', 'exists:program_levels,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('courses', 'code')->where(function ($query) use ($request) {
                    return $query->where('program_level_id', $request->program_level_id);
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'credit_hours' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        Course::create([
            'program_level_id' => $validated['program_level_id'],
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'credit_hours' => $validated['credit_hours'] ?? 0,
            'description' => isset($validated['description']) ? trim($validated['description']) : null,
        ]);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'program_level_id' => ['required', 'exists:program_levels,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('courses', 'code')
                    ->where(function ($query) use ($request) {
                        return $query->where('program_level_id', $request->program_level_id);
                    })
                    ->ignore($course->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'credit_hours' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $course->update([
            'program_level_id' => $validated['program_level_id'],
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'credit_hours' => $validated['credit_hours'] ?? 0,
            'description' => isset($validated['description']) ? trim($validated['description']) : null,
        ]);

        return redirect()
            ->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()
            ->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}