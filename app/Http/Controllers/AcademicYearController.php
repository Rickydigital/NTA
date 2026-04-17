<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:academic-year.view')->only('index');
        $this->middleware('permission:academic-year.create')->only('store');
        $this->middleware('permission:academic-year.update')->only('update');
        $this->middleware('permission:academic-year.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $academicYears = AcademicYear::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('start_date', 'like', "%{$search}%")
                        ->orWhere('end_date', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('academic-years.index', compact('academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_current')) {
            AcademicYear::query()->update(['is_current' => false]);
        }

        AcademicYear::create([
            'name' => trim($validated['name']),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $request->boolean('is_current'),
        ]);

        return redirect()
            ->route('academic-years.index')
            ->with('success', 'Academic year created successfully.');
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_years', 'name')->ignore($academicYear->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_current')) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_current' => false]);
        }

        $academicYear->update([
            'name' => trim($validated['name']),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $request->boolean('is_current'),
        ]);

        return redirect()
            ->route('academic-years.index')
            ->with('success', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->is_current) {
            return redirect()
                ->route('academic-years.index')
                ->with('error', 'Current academic year cannot be deleted.');
        }

        $academicYear->delete();

        return redirect()
            ->route('academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }
}