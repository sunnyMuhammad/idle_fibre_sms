<?php

namespace App\Http\Controllers\Backend;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\RequisitionProduct;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\RequisitionReceivedRequest;

class RequisitionApproveReceiveController extends Controller
{

    //requisition received request
    public function requisitionReceivedRequest(Request $request)
    {

        try {
            $productId = RequisitionProduct::where('id', $request->requisition_product_id)->first()->product_id;
            $categoryId = Product::where('id', $productId)->first()->category_id;
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
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }
    //requisition approved request
    public function requisitionApproveRequest(Request $request)
    {

        try {
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
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }

    // update requisition request
    public function updateRequisitionRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'received_qty' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }
        try {
            RequisitionReceivedRequest::where('id', $request->id)->update([
                'received_qty' => $request->received_qty,
            ]);
            return redirect()->back()->with(['status' => true, 'message' => 'Request updated successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }
}
