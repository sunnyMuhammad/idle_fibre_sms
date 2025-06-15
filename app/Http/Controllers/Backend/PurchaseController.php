<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Inertia\Inertia;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PurchaseProduct;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;


class PurchaseController extends Controller
{

    //create purchase
    public function createPurchase(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'unit' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'product_id' => 'required',
            'vendor_id' => 'required',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }

        $data = [
            'product_id' => $request->product_id,
            'unit' => $request->unit,
            'price' => $request->price,
            'reqisition_no' => $request->reqisition_no,
            'vendor_id' => $request->vendor_id,
            'brand_name' => $request->brand_name,
        ];
        try {
            PurchaseProduct::create($data);
            return redirect()->back()->with(['status' => true, 'message' => 'Purchase created successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }

    //update purchase
    public function updatePurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'product_id' => 'required',
            'vendor_id' => 'required',
            'requisiton_no' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }

        $data = [
            'product_id' => $request->product_id,
            'unit' => $request->unit,
            'price' => $request->price,
            'reqisition_no' => $request->reqisition_no,
            'vendor_id' => $request->vendor_id,
            'brand_name' => $request->brand_name,
        ];
        try {
            PurchaseProduct::where('id', $request->purchase_id)->update($data);
            return redirect()->back()->with(['status' => true, 'message' => 'Purchase updated successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }

    //delete purchase
    public function deletePurchase(Request $request)
    {
        try {
            PurchaseProduct::where('id', $request->purchase_id)->delete();
            return redirect()->back()->with(['status' => true, 'message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }
}
