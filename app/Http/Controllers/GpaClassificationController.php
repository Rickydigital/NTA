<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\GpaClassification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GpaClassificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:gpa-classification.view')->only('index');
        $this->middleware('permission:gpa-classification.create')->only('store');
        $this->middleware('permission:gpa-classification.update')->only('update');
        $this->middleware('permission:gpa-classification.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $gpaClassifications = GpaClassification::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('classification_code', 'like', "%{$search}%")
                        ->orWhere('final_comment', 'like', "%{$search}%")
                        ->orWhere('progression_action', 'like', "%{$search}%");
                });
            })
            ->orderBy('priority_order')
            ->orderByDesc('max_gpa')
            ->paginate(10)
            ->withQueryString();

        return view('gpa.classifications', compact('gpaClassifications'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'min_gpa' => ['required', 'numeric', 'min:0'],
            'max_gpa' => ['required', 'numeric', 'min:0'],
            'classification_code' => ['required', 'string', 'max:100', 'unique:gpa_classifications,classification_code'],
            'final_comment' => ['required', 'string', 'max:255'],
            'progression_action' => ['nullable', 'string', 'max:100'],
            'priority_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ((float) $validated['min_gpa'] > (float) $validated['max_gpa']) {
            return back()
                ->withInput()
                ->with('error', 'Minimum GPA cannot be greater than maximum GPA.');
        }

        GpaClassification::create([
            'name' => trim($validated['name']),
            'min_gpa' => $validated['min_gpa'],
            'max_gpa' => $validated['max_gpa'],
            'classification_code' => strtoupper(trim($validated['classification_code'])),
            'final_comment' => trim($validated['final_comment']),
            'progression_action' => isset($validated['progression_action']) ? trim($validated['progression_action']) : null,
            'priority_order' => $validated['priority_order'] ?? 0,
        ]);

        return redirect()
            ->route('gpa-classifications.index')
            ->with('success', 'GPA classification created successfully.');
    }

    public function update(Request $request, GpaClassification $gpaClassification): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'min_gpa' => ['required', 'numeric', 'min:0'],
            'max_gpa' => ['required', 'numeric', 'min:0'],
            'classification_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('gpa_classifications', 'classification_code')->ignore($gpaClassification->id),
            ],
            'final_comment' => ['required', 'string', 'max:255'],
            'progression_action' => ['nullable', 'string', 'max:100'],
            'priority_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ((float) $validated['min_gpa'] > (float) $validated['max_gpa']) {
            return back()
                ->withInput()
                ->with('error', 'Minimum GPA cannot be greater than maximum GPA.');
        }

        $gpaClassification->update([
            'name' => trim($validated['name']),
            'min_gpa' => $validated['min_gpa'],
            'max_gpa' => $validated['max_gpa'],
            'classification_code' => strtoupper(trim($validated['classification_code'])),
            'final_comment' => trim($validated['final_comment']),
            'progression_action' => isset($validated['progression_action']) ? trim($validated['progression_action']) : null,
            'priority_order' => $validated['priority_order'] ?? 0,
        ]);

        return redirect()
            ->route('gpa-classifications.index')
            ->with('success', 'GPA classification updated successfully.');
    }

    public function destroy(GpaClassification $gpaClassification): RedirectResponse
    {
        $gpaClassification->delete();

        return redirect()
            ->route('gpa-classifications.index')
            ->with('success', 'GPA classification deleted successfully.');
    }
}