<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\RequisitionProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\RequisitionReceivedRequest;

class RequisitionController extends Controller
{
    //list requisition
    public function listRequisition(Request $request)
    {
        $fromDate = $request->query('fromDate');
        $toDate = $request->query('toDate');

        $requisitions = Requisition::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $fd = date('Y-m-d', strtotime($fromDate));
            $td = date('Y-m-d', strtotime($toDate));

            $query->whereDate('created_at', '>=', $fd)
                ->whereDate('created_at', '<=', $td);
        })->with('requisitionProducts.product')->select('id','created_by','created_at')->latest()->paginate(1000);

        return Inertia::render('Requisition/RequisitionListPage', ['requisitions' => $requisitions]);

    }
    //create requisition
    public function createRequisition(Request $request)
    {
        DB::beginTransaction();
        try {
            $created_by=$request->session()->get('user_name');
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

    //requisiton save page
    public function requisitionSavePage()
    {
        $products = Product::with('category')->select('id', 'name', 'unit' , 'unit_type' , 'category_id','brand_name' )->latest()->get();
        $minimumProducts = Product::whereColumn('unit', '<=', 'minimum_stock')->with('category')->select('id', 'name', 'unit', 'unit_type', 'category_id','brand_name')->latest()->get();
        return Inertia::render('Requisition/RequisitionSavePage',['products'=>$products,'minimumProducts'=>$minimumProducts]);
    }

    //requisition received request
    public function requisitionReceivedRequest(Request $request)
    {

       try{
         $productId=RequisitionProduct::where('id', $request->requisition_product_id)->first()->product_id;
         $categoryId=Product::where('id', $productId)->first()->category_id;
         RequisitionReceivedRequest::create([
            'requisitionProduct_id' => $request->requisition_product_id,
            'received_qty' => $request->received_qty,
            'product_id' => $productId,
            'category_id' => $categoryId,
        ]);
        RequisitionProduct::where('id', $request->requisition_product_id)->update([
            'status' => 'received',
        ]);
        return redirect()->back()->with(['status' => true, 'message' => 'Request sent successfully']);
       }catch(Exception $e){
           return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
       }
    }

    //requisition approved request
    public function requisitionApproveRequest(Request $request)
    {

        try{
                  $receivedRequest = RequisitionReceivedRequest::findOrFail($request->query('received_id'));

        // Update status
        $receivedRequest->update([
            'status' => $request->query('status'),
        ]);

        if ($request->status === 'approved') {
            $qty = $receivedRequest->received_qty;

            // Update requisition product received count
            RequisitionProduct::where('id', $receivedRequest->requisitionProduct_id)
                ->increment('total_received', $qty);

            // Update product stock
            Product::where('id', $receivedRequest->product_id)
                ->increment('unit', $qty);
        }

        return redirect()->back()->with(['status' => true, 'message' => "Request {$request->query('status')} successfully"]);
        }catch(Exception $e){
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }



    //requisition received request list
    public function requisitionReceivedRequestList()
    {
        $recievedRequests = RequisitionReceivedRequest::where('status', 'pending')->with('product', 'requisitionProduct.requisition')->get();
        return Inertia::render('Requisition/RequisitionReceivedRequestPage', ['recievedRequests' => $recievedRequests]);
    }

    //requisition product list
    public function requisitionProductList()
    {
        $requisitionProducts = RequisitionProduct::where('status', 'pending')->with('product', 'requisition')->get();
        return Inertia::render('Requisition/RequisitionProductPage', ['requisitionProducts' => $requisitionProducts]);

    }

    //edit requisition request page
    public function editRequisitionRequestPage(Request $request)
    {
        $requisition = RequisitionReceivedRequest::where('id', $request->id)->with('product')->first();
        return Inertia::render('Requisition/EditRequisitionRequestPage', ['requisition' => $requisition]);
    }

    // update requisition request
    public function updateRequisitionRequest(Request $request){
           $validator = Validator::make($request->all(), [
            'received_qty' => 'required'
           ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }
        try{
            RequisitionReceivedRequest::where('id', $request->id)->update([
                'received_qty' => $request->received_qty,
            ]);
            return redirect()->back()->with(['status' => true, 'message' => 'Request updated successfully']);
        }catch(Exception $e){
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
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
