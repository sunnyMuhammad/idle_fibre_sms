<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PurchaseProduct;
use App\Models\Vendor;
use Illuminate\Support\Facades\Validator;


class PurchaseController extends Controller
{
    //list purchase
    public function listPurchase(Request $request)
    {
        $fromDate = $request->query('fromDate');
        $toDate = $request->query('toDate');
        $purchases = PurchaseProduct::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $fd = date('Y-m-d', strtotime($fromDate));
            $td = date('Y-m-d', strtotime($toDate));
            $query->whereDate('created_at', '>=', $fd)
                ->whereDate('created_at', '<=', $td);
        })->select('id', 'product_name', 'brand_name', 'unit', 'unit_type', 'price', 'reqisition_no', 'vendor_name', 'address', 'phone', 'created_at')
        ->latest()->paginate(1000);
        return Inertia::render('Purchase/PurchaseListPage', ['purchases' => $purchases]);
    }

    //purchase save page
    public function purchaseSavePage(Request $request)
    {
        $products = Product::with('category')->select('id', 'name', 'unit', 'unit_type', 'category_id', 'brand_name', 'parts_no')->get();
        $purchase_id = $request->query('purchase_id');
        $vendors=Vendor::all();
        $purchaseProduct = PurchaseProduct::where('id', $purchase_id)->first();
        return Inertia::render('Purchase/PurchaseSavePage', ['purchaseProduct' => $purchaseProduct, 'products' => $products,'','vendors'=>$vendors]);
    }

    //create purchase
    public function createPurchase(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'unit' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }
        $unit_type = Product::where('id', $request->selected_product_id)->first()->unit_type;
        $data = [
            'product_name' => $request->product_name,
            'unit' => $request->unit,
            'unit_type' => $unit_type,
            'price' => $request->price,
            'reqisition_no' => $request->reqisition_no,
            'vendor_name' => $request->vendor_name,
            'brand_name' => $request->brand_name,
            'address' => $request->address,
            'phone' => $request->phone
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }

        $data = [
            'product_name' => $request->product_name,
            'unit' => $request->unit,
            'unit_type' => $request->unit_type,
            'price' => $request->price,
            'reqisition_no' => $request->reqisition_no,
            'vendor_name' => $request->vendor_name,
            'brand_name' => $request->brand_name,
            'address' => $request->address,
            'phone' => $request->phone
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
