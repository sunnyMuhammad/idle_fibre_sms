<?php

namespace App\Http\Controllers\Backend;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\IssueProduct;
use Illuminate\Http\Request;
use App\Models\DamageProduct;
use App\Http\Controllers\Controller;

class ProductIssueController extends Controller
{


    //issue product
    public function issueProduct(Request $request)
    {
        try {
            $product = Product::findOrFail($request->product_id);
            $category_id = $product->category_id;
            $exist_qty = $product->unit;
            $damage_qty = $request->damage ?? 0;
            $issue_qty = $request->issue ?? 0;


            if ($damage_qty > 0) {
                DamageProduct::create([
                    'product_id' => $product->id,
                    'unit' => $damage_qty,
                    'category_id' => $category_id,
                ]);
            }

            // If only damage was done and no issue
            if ($issue_qty == 0 && $damage_qty > 0) {
                return redirect()->back()->with(['status' => true, 'message' => 'Product damaged successfully']);
            }


            if ($issue_qty > 0) {
                if ($issue_qty <= $exist_qty) {
                    IssueProduct::create([
                        'product_id' => $product->id,
                        'unit' => $issue_qty,
                        'category_id' => $category_id,
                        'machine_name' => $request->machine_name
                    ]);

                    $product->decrement('unit', $issue_qty);

                    return redirect()->back()->with(['status' => true, 'message' => 'Product issued successfully']);
                } else {
                    return redirect()->back()->with(['status' => false, 'message' => 'Issue quantity is greater than available quantity']);
                }
            }
            return redirect()->back()->with(['status' => false, 'message' => 'No issue or damage quantity provided']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
