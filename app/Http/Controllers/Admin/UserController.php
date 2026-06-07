<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manage-users');

        $users = User::with('roles')
            ->when($request->search, fn ($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->when($request->role, fn ($q) =>
                $q->whereHas('roles', fn ($r) => $r->where('name', $request->role))
            )
            ->when($request->status !== null && $request->status !== '', fn ($q) =>
                $q->where('is_active', (bool) $request->status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $this->authorize('manage-users');

        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'role'                  => ['required', 'exists:roles,name'],
            'is_active'             => ['boolean'],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);
        $user->assignRole($data['role']);

        AuditLogService::log('created', 'users', "Created user: {$user->name} ({$user->email}) with role {$data['role']}");

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} created successfully.");
    }

    public function show(User $user)
    {
        $this->authorize('manage-users');

        $user->load('roles', 'roles.permissions');
        $recentLogs = AuditLog::where('user_id', $user->id)
            ->latest()->limit(10)->get();

        return view('admin.users.show', compact('user', 'recentLogs'));
    }

    public function edit(User $user)
    {
        $this->authorize('manage-users');

        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', "unique:users,email,{$user->id}"],
            'password'              => ['nullable', 'min:8', 'confirmed'],
            'role'                  => ['required', 'exists:roles,name'],
            'is_active'             => ['boolean'],
        ]);

        $old = $user->only('name', 'email', 'is_active');

        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (! empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles([$data['role']]);

        AuditLogService::log('updated', 'users', "Updated user: {$user->name}", $old, $user->fresh()->only('name', 'email', 'is_active'));

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} updated successfully.");
    }

    public function destroy(User $user)
    {
        $this->authorize('manage-users');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        AuditLogService::log('deleted', 'users', "Deleted user: {$name}");

        return redirect()->route('admin.users.index')
            ->with('success', "User {$name} deleted.");
    }
}
