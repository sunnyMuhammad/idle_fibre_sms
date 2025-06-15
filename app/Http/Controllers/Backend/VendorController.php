<?php

namespace App\Http\Controllers\Backend;

use Inertia\Inertia;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class VendorController extends Controller
{
    //create vendor
    public function createVendor(Request $request)  {

        $validation = Validator::make(request()->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        if($validation->fails()){
            return redirect()->back()->with(['errors' => $validation->errors()]);
        }

        $data=[
            'name'=>$request->name,
            'phone'=>$request->phone,
            'address'=>$request->address,
        ];

        try {
            Vendor::create($data);
            return redirect()->back()->with(['status' => true, 'message' => 'Vendor created successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }

    //update vendor
    public function updateVendor(Request $request){
        $validation = Validator::make(request()->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        if($validation->fails()){
            return redirect()->back()->with(['errors' => $validation->errors()]);
        }

        try {
            $vendor=Vendor::findOrFail($request->vendor_id);
            $vendor->update([
                'name'=>$request->name,
                'phone'=>$request->phone,
                'address'=>$request->address,
            ]);
            return redirect()->back()->with(['status' => true, 'message' => 'Vendor updated successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }

    //delete vendor
    public function deleteVendor(Request $request){
        try {
            Vendor::where('id', $request->vendor_id)->delete();
            return redirect()->back()->with(['status' => true, 'message' => 'Vendor deleted successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
