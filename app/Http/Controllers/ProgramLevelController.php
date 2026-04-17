<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Program;
use App\Models\ProgramLevel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgramLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:program-level.view')->only('index');
        $this->middleware('permission:program-level.create')->only('store');
        $this->middleware('permission:program-level.update')->only('update');
        $this->middleware('permission:program-level.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $programs = Program::orderBy('name')->get();

        $levels = ProgramLevel::query()
            ->with('program')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('level_number', 'like', "%{$search}%")
                        ->orWhereHas('program', function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('program_id'), function ($query) use ($request) {
                $query->where('program_id', $request->program_id);
            })
            ->orderBy('program_id')
            ->orderBy('sort_order')
            ->orderBy('level_number')
            ->paginate(10)
            ->withQueryString();

        return view('programs.levels', compact('levels', 'programs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('program_levels', 'code')->where(function ($query) use ($request) {
                    return $query->where('program_id', $request->program_id);
                }),
            ],
            'level_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('program_levels', 'level_number')->where(function ($query) use ($request) {
                    return $query->where('program_id', $request->program_id);
                }),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        ProgramLevel::create([
            'program_id' => $validated['program_id'],
            'name' => trim($validated['name']),
            'code' => strtoupper(trim($validated['code'])),
            'level_number' => $validated['level_number'],
            'sort_order' => $validated['sort_order'] ?? $validated['level_number'],
        ]);

        return redirect()
            ->route('program-levels.index')
            ->with('success', 'Program level created successfully.');
    }

    public function update(Request $request, ProgramLevel $programLevel): RedirectResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('program_levels', 'code')
                    ->where(function ($query) use ($request) {
                        return $query->where('program_id', $request->program_id);
                    })
                    ->ignore($programLevel->id),
            ],
            'level_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('program_levels', 'level_number')
                    ->where(function ($query) use ($request) {
                        return $query->where('program_id', $request->program_id);
                    })
                    ->ignore($programLevel->id),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $programLevel->update([
            'program_id' => $validated['program_id'],
            'name' => trim($validated['name']),
            'code' => strtoupper(trim($validated['code'])),
            'level_number' => $validated['level_number'],
            'sort_order' => $validated['sort_order'] ?? $validated['level_number'],
        ]);

        return redirect()
            ->route('program-levels.index')
            ->with('success', 'Program level updated successfully.');
    }

    public function destroy(ProgramLevel $programLevel): RedirectResponse
    {
        $programLevel->delete();

        return redirect()
            ->route('program-levels.index')
            ->with('success', 'Program level deleted successfully.');
    }
}