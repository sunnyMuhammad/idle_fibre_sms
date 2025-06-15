<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class UserPageController extends Controller
{
    //list user
    public function listUser()
    {
        $users = User::with('roles')->get();
        return Inertia::render('Users/UserListPage', ['users' => $users]);
    }

    //user save page
    public function userSavePage(Request $request)
    {
        $userId = $request->user_id;
        $roles = Role::all();
        $users = User::find($userId);
        if ($userId != 0) {
            $users = User::with('roles')->find($userId);
        }
        return Inertia::render('Users/UserSavePage', ['users' => $users, 'roles' => $roles]);
    }
}
