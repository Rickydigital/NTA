<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Program;
use App\Models\ProgramLevel;
use App\Models\ProgressionRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgressionRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:progression-rule.view')->only('index');
        $this->middleware('permission:progression-rule.create')->only('store');
        $this->middleware('permission:progression-rule.update')->only('update');
        $this->middleware('permission:progression-rule.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $programs = Program::orderBy('name')->get();

        $programLevels = ProgramLevel::with('program')
            ->orderBy('program_id')
            ->orderBy('sort_order')
            ->orderBy('level_number')
            ->get();

        $rules = ProgressionRule::query()
            ->with(['program', 'fromLevel.program', 'toLevel.program'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('decision', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('program', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('fromLevel', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('toLevel', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('program_id'), function ($query) use ($request) {
                $query->where('program_id', $request->program_id);
            })
            ->when($request->filled('decision'), function ($query) use ($request) {
                $query->where('decision', $request->decision);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('progression.index', compact('rules', 'programs', 'programLevels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'from_program_level_id' => ['required', 'exists:program_levels,id'],
            'to_program_level_id' => ['nullable', 'exists:program_levels,id'],
            'min_gpa_required' => ['nullable', 'numeric', 'min:0'],
            'max_failed_courses_allowed' => ['nullable', 'integer', 'min:0'],
            'blocked_by_disco' => ['nullable', 'boolean'],
            'blocked_by_fail_oral' => ['nullable', 'boolean'],
            'requires_manual_approval' => ['nullable', 'boolean'],
            'decision' => ['required', 'in:proceed,retained,disco,manual_review,completed'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->validateLevelsBelongToProgram(
            (int) $validated['program_id'],
            (int) $validated['from_program_level_id'],
            isset($validated['to_program_level_id']) ? (int) $validated['to_program_level_id'] : null
        );

        ProgressionRule::create([
            'program_id' => $validated['program_id'],
            'from_program_level_id' => $validated['from_program_level_id'],
            'to_program_level_id' => $validated['to_program_level_id'] ?? null,
            'min_gpa_required' => $validated['min_gpa_required'] ?? null,
            'max_failed_courses_allowed' => $validated['max_failed_courses_allowed'] ?? 0,
            'blocked_by_disco' => $request->boolean('blocked_by_disco', true),
            'blocked_by_fail_oral' => $request->boolean('blocked_by_fail_oral', true),
            'requires_manual_approval' => $request->boolean('requires_manual_approval'),
            'decision' => $validated['decision'],
            'notes' => isset($validated['notes']) ? trim($validated['notes']) : null,
        ]);

        return redirect()
            ->route('progression-rules.index')
            ->with('success', 'Progression rule created successfully.');
    }

    public function update(Request $request, ProgressionRule $progressionRule): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'from_program_level_id' => ['required', 'exists:program_levels,id'],
            'to_program_level_id' => ['nullable', 'exists:program_levels,id'],
            'min_gpa_required' => ['nullable', 'numeric', 'min:0'],
            'max_failed_courses_allowed' => ['nullable', 'integer', 'min:0'],
            'blocked_by_disco' => ['nullable', 'boolean'],
            'blocked_by_fail_oral' => ['nullable', 'boolean'],
            'requires_manual_approval' => ['nullable', 'boolean'],
            'decision' => [
                'required',
                'in:proceed,retained,disco,manual_review,completed',
                Rule::unique('progression_rules', 'decision')
                    ->where(function ($query) use ($request) {
                        return $query
                            ->where('program_id', $request->program_id)
                            ->where('from_program_level_id', $request->from_program_level_id);
                    })
                    ->ignore($progressionRule->id),
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $this->validateLevelsBelongToProgram(
            (int) $validated['program_id'],
            (int) $validated['from_program_level_id'],
            isset($validated['to_program_level_id']) ? (int) $validated['to_program_level_id'] : null
        );

        $progressionRule->update([
            'program_id' => $validated['program_id'],
            'from_program_level_id' => $validated['from_program_level_id'],
            'to_program_level_id' => $validated['to_program_level_id'] ?? null,
            'min_gpa_required' => $validated['min_gpa_required'] ?? null,
            'max_failed_courses_allowed' => $validated['max_failed_courses_allowed'] ?? 0,
            'blocked_by_disco' => $request->boolean('blocked_by_disco'),
            'blocked_by_fail_oral' => $request->boolean('blocked_by_fail_oral'),
            'requires_manual_approval' => $request->boolean('requires_manual_approval'),
            'decision' => $validated['decision'],
            'notes' => isset($validated['notes']) ? trim($validated['notes']) : null,
        ]);

        return redirect()
            ->route('progression-rules.index')
            ->with('success', 'Progression rule updated successfully.');
    }

    public function destroy(ProgressionRule $progressionRule): RedirectResponse
    {
        $progressionRule->delete();

        return redirect()
            ->route('progression-rules.index')
            ->with('success', 'Progression rule deleted successfully.');
    }

    private function validateLevelsBelongToProgram(int $programId, int $fromLevelId, ?int $toLevelId): void
    {
        $fromValid = ProgramLevel::where('id', $fromLevelId)
            ->where('program_id', $programId)
            ->exists();

        abort_unless($fromValid, 422, 'Selected from level does not belong to the selected program.');

        if ($toLevelId !== null) {
            $toValid = ProgramLevel::where('id', $toLevelId)
                ->where('program_id', $programId)
                ->exists();

            abort_unless($toValid, 422, 'Selected to level does not belong to the selected program.');
        }
    }
}