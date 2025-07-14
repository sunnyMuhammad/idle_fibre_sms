<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $user = Auth::user();

        $permissions = Permission::all();
        $can = [];

        foreach ($permissions as $permission) {
            $can[$permission->name] = $user && $user->can($permission->name);
        }

        return [
            'user' => [
                'user_name' => $user ? $user->name : null,
                'can' => $can,
            ],
            'flash' => [
                'status' => $request->session()->pull('status'),
                'message' => $request->session()->pull('message'),
                'errors' => $request->session()->pull('errors')
            ]
        ];
    }
}
