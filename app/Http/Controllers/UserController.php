<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user.view')->only('index');
        $this->middleware('permission:user.create')->only('store');
        $this->middleware('permission:user.update')->only('update');
        $this->middleware('permission:user.delete')->only('destroy');
    }

    public function index(Request $request): View
    {
        $roles = Role::orderBy('name')->get();

        $users = User::query()
            ->with('roles')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('second_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_no', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->role($request->role);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'second_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'phone_no' => ['nullable', 'string', 'max:30'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'first_name' => trim($validated['first_name']),
            'second_name' => isset($validated['second_name']) ? trim($validated['second_name']) : null,
            'last_name' => trim($validated['last_name']),
            'gender' => $validated['gender'] ?? null,
            'phone_no' => isset($validated['phone_no']) ? trim($validated['phone_no']) : null,
            'username' => trim($validated['username']),
            'email' => isset($validated['email']) ? trim($validated['email']) : null,
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'second_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'phone_no' => ['nullable', 'string', 'max:30'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $data = [
            'first_name' => trim($validated['first_name']),
            'second_name' => isset($validated['second_name']) ? trim($validated['second_name']) : null,
            'last_name' => trim($validated['last_name']),
            'gender' => $validated['gender'] ?? null,
            'phone_no' => isset($validated['phone_no']) ? trim($validated['phone_no']) : null,
            'username' => trim($validated['username']),
            'email' => isset($validated['email']) ? trim($validated['email']) : null,
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}