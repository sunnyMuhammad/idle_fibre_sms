<?php

namespace App\Http\Middleware;

use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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
        $user = null;

        try {
            $token = $request->cookie('token');

            if ($token) {
                $decoded = JWT::decode($token, new Key(env('JWT_KEY'), 'HS256'));
                $user=User::where('email',$decoded->userEmail)->first();
            }
        } catch (Exception $e) {

        }
        $permissions=Permission::all();
        $can = [];
        foreach ($permissions as $permission) {
            $can[$permission->name] = $user?($user->can($permission->name)? true:false):false;
        }

        return [
            'user' => [
                'user_name' => Auth::user() ? Auth::user()->name : null,
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
