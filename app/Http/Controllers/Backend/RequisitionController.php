<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\RequisitionProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\RequisitionReceivedRequest;

class RequisitionController extends Controller
{

    //create requisition
    public function createRequisition(Request $request)
    {
        DB::beginTransaction();
        try {
            $created_by=Auth::user()->name;
            $data = [
                'created_by' => $created_by
            ];
            $products = $request->products;
            $requisition = Requisition::create($data);
            foreach ($products as $product) {
                RequisitionProduct::create([
                    'requisition_id' => $requisition->id,
                    'product_id' => $product['id'],
                    'total_requisition' => $product['requisition_qty'],
                    'total_received' => 0,
                    'remarks' => $product['remarks'],
                    'where_to_use' => $product['where_to_use'],
                    'store_code' => $product['store_code'],
                    'size' => $product['size'],
                ]);
            }
            DB::commit();
            return redirect()->back()->with(['status' => true, 'message' => 'Requisition created successfully']);
        } catch (Exception $e) {
            DB::rollBack();
           return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong'.$e->getMessage()]);
        }
    }


    //delete requistion
    public function deleteRequisition(Request $request)
    {

        try {
            RequisitionProduct::where('requisition_id', $request->requisition_id)->delete();
            Requisition::where('id', $request->requisition_id)->delete();

            return redirect()->back()->with(['status' => true, 'message' => 'Requisition deleted successfully']);
        } catch (Exception $e) {

            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }
}
