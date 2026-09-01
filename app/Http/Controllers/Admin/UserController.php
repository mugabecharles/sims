<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(private AuditService $audit) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::with('roles')->withTrashed()->paginate(25),
            'roles' => Role::orderBy('display_name')->get(['id', 'name', 'display_name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::orderBy('display_name')->get(['id', 'name', 'display_name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:191'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users'],
            'email'    => ['nullable', 'email', 'max:191', 'unique:users'],
            'phone'    => ['nullable', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status'   => ['required', 'in:active,inactive,suspended'],
            'roles'    => ['nullable', 'array'],
            'roles.*'  => ['exists:roles,id'],
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        if (! empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        $this->audit->created($user, 'User');

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user): Response
    {
        return Inertia::render('Admin/Users/Show', [
            'user'  => $user->load('roles'),
            'roles' => Role::orderBy('display_name')->get(['id', 'name', 'display_name']),
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user'  => $user->load('roles'),
            'roles' => Role::orderBy('display_name')->get(['id', 'name', 'display_name']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:191'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'email'    => ['nullable', 'email', 'max:191', 'unique:users,email,' . $user->id],
            'phone'    => ['nullable', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'status'   => ['required', 'in:active,inactive,suspended,locked'],
        ]);

        $original = $user->toArray();
        $user->update($data);
        $this->audit->updated($user, $original, 'User');

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->audit->deleted($user, null, 'User');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deactivated.');
    }

    public function syncRoles(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'roles'   => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $original = $user->roles()->pluck('id')->toArray();
        $user->roles()->sync($data['roles']);
        $this->audit->log('roles_synced', null, $user, ['roles' => $original], ['roles' => $data['roles']], null, 'User');

        return back()->with('success', 'User roles updated.');
    }
}
