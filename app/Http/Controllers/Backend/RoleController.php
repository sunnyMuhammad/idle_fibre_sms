<?php

namespace App\Http\Controllers\Backend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
//use App\Models\Product;

class RoleController extends Controller
{

    //create role
    public function createRole(Request $request){

        // $product = Product::all();
        // foreach($product as $p){
        //     Product::where('id', $p->id)->update(['minimum_stock' => 0]);
        // }

        $validation = Validator::make($request->all(), [
            'roleName' => 'required|unique:roles,name',
        ]);

        if($validation->fails()){
            return redirect()->back()->with(['errors' => $validation->errors()]);
        }

        $role=Role::create([
            'name'=>$request->roleName,
        ]);
        $permissionId=array_map('intval', $request->permissions);
        $role->syncPermissions($permissionId);
        return redirect('/list-role')->with(['status'=>true,'message'=>'Role has been created successfully']);
    }

    //update role
    public function updateRole(Request $request){

        $validation = Validator::make($request->all(), [
            'roleName' => 'required|unique:roles,name,'.$request->role_id,
        ]);

        if($validation->fails()){
            return redirect()->back()->with(['errors' => $validation->errors()]);
        }
        $role=Role::where('id',$request->role_id)->first();
        if($role->name=='superadmin'){
            return redirect()->back()->with(['status' => false, 'message' => 'Superadmin role cannot be updated']);
        }
        $role->update([
            'name'=>$request->roleName,
        ]);
        $permissionId=array_map('intval', $request->permissions);
        $role->syncPermissions($permissionId);
        return redirect('/list-role')->with(['status'=>true,'message'=>'Role has been updated successfully']);
    }

    //delete role
    public function deleteRole(Request $request){
        $role=Role::where('id',$request->role_id)->first();
        if($role->name=='superadmin'){
            return redirect()->back()->with(['status' => false, 'message' => 'Superadmin role cannot be deleted']);
        }
        $role->delete();
        return redirect('/list-role')->with(['status'=>true,'message'=>'Role has been deleted successfully']);
    }


}
