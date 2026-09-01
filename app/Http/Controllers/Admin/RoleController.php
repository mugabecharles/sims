<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function __construct(private AuditService $audit) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles'       => Role::with('permissions')->get(),
            'permissions' => Permission::orderBy('module')->orderBy('display_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:50', 'unique:roles'],
            'display_name' => ['required', 'string', 'max:100'],
            'description'  => ['nullable', 'string'],
        ]);

        $role = Role::create($data);
        $this->audit->created($role, 'Role');

        return back()->with('success', "Role '{$data['display_name']}' created.");
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'description'  => ['nullable', 'string'],
        ]);

        $original = $role->toArray();
        $role->update($data);
        $this->audit->updated($role, $original, 'Role');

        return back()->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        $this->audit->deleted($role, null, 'Role');
        $role->delete();

        return back()->with('success', 'Role deleted.');
    }

    public function syncPermissions(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $original = $role->permissions()->pluck('id')->toArray();
        $role->permissions()->sync($data['permissions']);
        $this->audit->log('permissions_synced', null, $role, ['permissions' => $original], ['permissions' => $data['permissions']], null, 'Role');

        return back()->with('success', "Permissions updated for role '{$role->display_name}'.");
    }
}
