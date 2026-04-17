<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:program.view')->only('index');
        $this->middleware('permission:program.create')->only('store');
        $this->middleware('permission:program.update')->only('update');
        $this->middleware('permission:program.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $programs = Program::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('programs.index', compact('programs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:programs,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Program::create([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'description' => isset($validated['description']) ? trim($validated['description']) : null,
        ]);

        return redirect()
            ->route('programs.index')
            ->with('success', 'Program created successfully.');
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('programs', 'code')->ignore($program->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $program->update([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'description' => isset($validated['description']) ? trim($validated['description']) : null,
        ]);

        return redirect()
            ->route('programs.index')
            ->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return redirect()
            ->route('programs.index')
            ->with('success', 'Program deleted successfully.');
    }
}