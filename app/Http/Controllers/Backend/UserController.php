<?php

namespace App\Http\Controllers\Backend;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //create user
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone

            ];
            $user=User::create($data);
            $user->assignRole($request->role);

            return redirect()->back()->with(['status' => true, 'message' => 'User created successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong' . $e->getMessage()]);
        }
    }

    //update user

    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }
        try {
            User::where('id', $request->user_id)->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'phone' => $request->phone
            ]);
            $user=User::find($request->user_id)->with('roles');
            $userRole = count($user->roles)!=0 ? $user->roles[0]->name : null;
            if ($userRole != 'superadmin' || $userRole == null) {
                $user->syncRoles($request->role);
            }

            return redirect()->back()->with(['status' => true, 'message' => 'User updated successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }

    //delete user
    public function deleteUser(Request $request)
    {
        $user = User::where('id', $request->user_id)->with('roles')->first();

        $userRole = count($user->roles)!=0 ? $user->roles[0]->name : null;
        if ($userRole == 'superadmin') {

            return redirect()->back()->with(['status' => false, 'message' => 'You can not delete This User']);
        }
        User::where('id', $request->user_id)->delete();
        return redirect()->back()->with(['status' => true, 'message' => 'User deleted successfully']);
    }
}
