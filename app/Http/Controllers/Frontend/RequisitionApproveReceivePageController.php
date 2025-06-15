<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RequisitionReceivedRequest;

class RequisitionApproveReceivePageController extends Controller
{
      //requisition received request list
    public function requisitionReceivedRequestList()
    {
        $recievedRequests = RequisitionReceivedRequest::where('status', 'pending')->with('product', 'requisitionProduct.requisition')->get();
        return Inertia::render('Requisition/RequisitionReceivedRequestPage', ['recievedRequests' => $recievedRequests]);
    }

     //edit requisition request page
    public function editRequisitionRequestPage(Request $request)
    {
        $requisition = RequisitionReceivedRequest::where('id', $request->id)->with('product')->first();
        return Inertia::render('Requisition/EditRequisitionRequestPage', ['requisition' => $requisition]);
    }

}
