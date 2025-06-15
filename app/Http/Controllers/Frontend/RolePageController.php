<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RolePageController extends Controller
{
    //role list
    public function roleList()
    {

        $roles = Role::with('permissions')->get();
        return Inertia::render('Roles/RoleListPage', ['roles' => $roles]);
    }

    //role save page
    public function roleSavePage(Request $request)
    {
        $roleId = $request->role_id;
        $permissions = Permission::all();
        $role = Role::where('id', $roleId)->with('permissions')->first();
        return Inertia::render('Roles/RoleSavePage', ['role' => $role, 'permissions' => $permissions]);
    }
}
