<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\RequisitionProduct;
use App\Http\Controllers\Controller;

class RequisitionPageController extends Controller
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
        })->with('requisitionProducts.product')->select('id', 'created_by', 'created_at')
        ->latest()->paginate(500)->withQueryString();

        return Inertia::render('Requisition/RequisitionListPage', ['requisitions' => $requisitions]);
    }

    //requisiton save page
    public function requisitionSavePage()
    {

        return Inertia::render('Requisition/RequisitionSavePage',);
    }

     //requisition product list
    public function requisitionProductList()
    {
        $requisitionProducts = RequisitionProduct::where('status', 'pending')->with('product', 'requisition')->get();
        return Inertia::render('Requisition/RequisitionProductPage', ['requisitionProducts' => $requisitionProducts]);

    }
}
