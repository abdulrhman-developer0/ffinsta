<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function index(Request $request)
    {
        $admins = User::where('role', 'admin')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'permissions' => ['nullable', 'array'],
        ]);

        $admin = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => 'admin',
            'permissions' => $request->permissions ?? [],
        ]);

        $this->activityLogService->log('create_admin', "Created admin: {$admin->name} ({$admin->email})", 'user', $admin->id);

        return redirect()->route('admin.admins.index')->with('success', __('Admin created successfully.'));
    }

    public function edit(User $admin)
    {
        abort_if(!$admin->isAdmin(), 404);
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        abort_if(!$admin->isAdmin(), 404);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'permissions' => ['nullable', 'array'],
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($admin->id !== 1) {
            $admin->permissions = $request->permissions ?? [];
        }

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        $this->activityLogService->log('update_admin', "Updated admin: {$admin->name} ({$admin->email})", 'user', $admin->id);

        return redirect()->route('admin.admins.index')->with('success', __('Admin updated successfully.'));
    }

    public function destroy(User $admin)
    {
        abort_if(!$admin->isAdmin(), 404);
        abort_if($admin->id === auth()->id(), 403, __('You cannot delete your own account.'));

        $this->activityLogService->log('delete_admin', "Deleted admin: {$admin->name} ({$admin->email})", 'user', $admin->id);

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('success', __('Admin deleted successfully.'));
    }
}
