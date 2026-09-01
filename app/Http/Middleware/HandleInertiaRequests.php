<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Data shared with every Inertia page.
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'roles'  => $user->roles()->pluck('name'),
                    'permissions' => $user->roles()
                        ->with('permissions')
                        ->get()
                        ->flatMap(fn($role) => $role->permissions->pluck('code'))
                        ->unique()
                        ->values(),
                ] : null,
            ],
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
                'warning' => session('warning'),
                'info'    => session('info'),
            ],
            'school' => $user
                ? $user->schools()->where('is_primary', true)->first()?->only([
                    'id', 'name', 'short_name', 'logo_url', 'level', 'school_type', 'currency', 'timezone',
                ])
                : null,
            // Base URL for building correct URLs in React when running in a subdirectory
            'base_url' => rtrim(config('app.url'), '/'),
        ]);
    }
}
