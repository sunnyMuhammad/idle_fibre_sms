<?php

namespace App\Http\Middleware;

use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Inertia\Middleware;
use Illuminate\Http\Request;

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


        $permissions = [
           'list-user',
            'create-user',
            'update-user',
            'delete-user',
            'list-role',
            'create-role',
            'update-role',
            'delete-role',
            'list-product',
            'create-product',
            'update-product',
            'delete-product',
            'list-category',
            'create-category',
            'update-category',
            'delete-category',
            'list-vendor',
            'create-vendor',
            'update-vendor',
            'delete-vendor',
            'list-purchase',
            'create-purchase',
            'update-purchase',
            'delete-purchase',
            'list-issue-product',
            'list-damage-product',
            'list-minimum-product',
            'list-requisition',
            'create-requisition',
            'approve-floor-receive',
            'approve-reuisition-receive',
            'receive-requisition',
            'receive-floor-receive',
            'issue-product',
            'delete-requisition',
            'product-stock-report',
            'product-report'
        ];

        $can = [];

        foreach ($permissions as $permission) {
            $can[$permission] = $user?($user->can($permission)? true:false):false;
        }

        return [
            'user' => [
                'role' => $request->session()->get('role'),
                'user_name' => $request->session()->get('user_name'),
                'can' => $can
            ],
            'flash' => [
                'status' => $request->session()->pull('status'),
                'message' => $request->session()->pull('message'),
                'errors' => $request->session()->pull('errors')
            ]
        ];
    }
}
