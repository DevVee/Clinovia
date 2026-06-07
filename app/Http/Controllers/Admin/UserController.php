<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
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

    /**
     * HIGH-5 FIX: Replaced inline validate() with StoreUserRequest which enforces
     * a strong password policy (10+ chars, mixed case, numbers, symbols, breach check).
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);
        $user->assignRole($data['role']);

        AuditLogService::log(
            'created',
            'users',
            "Created user: {$user->name} ({$user->email}) with role {$data['role']}"
        );

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

    /**
     * HIGH-5 FIX: Uses UpdateUserRequest with strong password policy.
     * MED-4 FIX: Cannot remove the last active administrator.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // MED-4 FIX: Prevent removing the last active administrator account
        if ($user->hasRole('administrator') && $data['role'] !== 'administrator') {
            $adminCount = User::role('administrator')
                ->where('is_active', true)
                ->where('id', '!=', $user->getKey())
                ->count();

            if ($adminCount === 0) {
                return back()->withErrors(['role' =>
                    'Cannot change the role of the last active administrator.'
                ]);
            }
        }

        // Also guard against deactivating the last admin
        $isBeingDeactivated = $user->is_active && ! $request->boolean('is_active', true);
        if ($user->hasRole('administrator') && $isBeingDeactivated) {
            $adminCount = User::role('administrator')
                ->where('is_active', true)
                ->where('id', '!=', $user->getKey())
                ->count();

            if ($adminCount === 0) {
                return back()->withErrors(['is_active' =>
                    'Cannot deactivate the last active administrator.'
                ]);
            }
        }

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

        AuditLogService::log(
            'updated',
            'users',
            "Updated user: {$user->name}",
            $old,
            $user->fresh()->only('name', 'email', 'is_active')
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} updated successfully.");
    }

    /**
     * MED-4 FIX: Prevents deleting the last active administrator account,
     * which would lock everyone out of the system.
     */
    public function destroy(User $user)
    {
        $this->authorize('manage-users');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Guard: ensure at least one other active administrator remains
        if ($user->hasRole('administrator')) {
            $remainingAdmins = User::role('administrator')
                ->where('is_active', true)
                ->where('id', '!=', $user->getKey())
                ->count();

            if ($remainingAdmins === 0) {
                return back()->with('error',
                    'Cannot delete the last administrator account. ' .
                    'Promote another user to administrator first.'
                );
            }
        }

        $name = $user->name;
        $user->delete();

        AuditLogService::log('deleted', 'users', "Deleted user: {$name}");

        return redirect()->route('admin.users.index')
            ->with('success', "User {$name} deleted.");
    }
}
