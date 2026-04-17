<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permission.view')->only('index');
    }

    public function index(Request $request): View
    {
        $permissions = Permission::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('users.permissions', compact('permissions'));
    }
}