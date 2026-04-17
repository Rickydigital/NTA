<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role.view')->only('index');
        $this->middleware('permission:role.create')->only('store');
        $this->middleware('permission:role.update')->only('update');
        $this->middleware('permission:role.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $permissions = Permission::orderBy('name')->get();

        $roles = Role::query()
            ->with('permissions')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.roles', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => trim($validated['name']),
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        if ($role->name === 'Super Admin' && trim($validated['name']) !== 'Super Admin') {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Super Admin role name cannot be changed.');
        }

        $role->update([
            'name' => trim($validated['name']),
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'Super Admin') {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Super Admin role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'This role is assigned to users and cannot be deleted.');
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}