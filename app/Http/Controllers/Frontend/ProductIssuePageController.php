<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\IssueProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductIssuePageController extends Controller
{
    // product issue page
    public function productIssuePage(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::where('id', $productId)->first();
        return Inertia::render('Products/ProductDamageIssuePage', ['product' => $product]);
    }
    //issue product list
    public function issueProductList(Request $request)
    {
        $fromDate = $request->query('fromDate');
        $toDate = $request->query('toDate');

        $issueProducts = IssueProduct::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $fd = date('Y-m-d', strtotime($fromDate));
            $td = date('Y-m-d', strtotime($toDate));

            $query->whereDate('created_at', '>=', $fd)
                ->whereDate('created_at', '<=', $td);
        })->with('product')->select('id', 'product_id', 'unit', 'category_id', 'machine_name', 'created_at')
        ->latest()->paginate(500)->withQueryString();

        return Inertia::render('Products/ProductIssuePage', ['issueProducts' => $issueProducts, 'fromDate' => $fromDate, 'toDate' => $toDate]);
    }
}
