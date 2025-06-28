<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PurchaseProduct;
use App\Http\Controllers\Controller;

class PurchasePageController extends Controller
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
        })->with('product.category', 'vendor')
            ->latest()->paginate(500)->withQueryString();
        return Inertia::render('Purchase/PurchaseListPage', ['purchases' => $purchases]);
    }

    //purchase save page
    public function purchaseSavePage(Request $request)
    {

        $vendors = Vendor::all();
        $purchase_id = $request->query('purchase_id');
        $purchaseProduct = PurchaseProduct::with('product')->where('id', $purchase_id)->first();
        return Inertia::render('Purchase/PurchaseSavePage', ['purchaseProduct' => $purchaseProduct,  'vendors' => $vendors]);
    }
}
