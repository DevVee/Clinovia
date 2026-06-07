<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /** Group a permission name by its subject (everything after the first verb). */
    private function groupPermission(string $name): string
    {
        $parts = explode('-', $name);
        array_shift($parts); // remove verb (view, create, manage, etc.)
        return implode('-', $parts) ?: 'general';
    }

    public function index()
    {
        $this->authorize('manage-roles');

        $roles = Role::with('permissions')
            ->withCount('users')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('manage-roles');

        $permissions = Permission::all()
            ->groupBy(fn ($p) => $this->groupPermission($p->name));

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-roles');

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:50', 'unique:roles,name', 'regex:/^[a-z0-9_\-]+$/'],
            'permissions' => ['array'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);

        if (! empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        AuditLogService::log('created', 'users', "Created role: {$role->name} with " . count($data['permissions'] ?? []) . ' permissions');

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    public function show(Role $role)
    {
        $this->authorize('manage-roles');

        $role->load('permissions');
        $role->loadCount('users');

        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $this->authorize('manage-roles');

        $permissions = Permission::all()
            ->groupBy(fn ($p) => $this->groupPermission($p->name));

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('manage-roles');

        $data = $request->validate([
            'permissions' => ['array'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        AuditLogService::log('updated', 'users', "Updated role '{$role->name}' permissions");

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' permissions updated.");
    }

    public function destroy(Role $role)
    {
        $this->authorize('manage-roles');

        if (in_array($role->name, ['administrator', 'nurse', 'staff'])) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        $name = $role->name;
        $role->delete();

        AuditLogService::log('deleted', 'users', "Deleted role: {$name}");

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$name}' deleted.");
    }
}
