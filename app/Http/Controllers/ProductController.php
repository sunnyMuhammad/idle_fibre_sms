<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Models\IssueProduct;
use Illuminate\Http\Request;
use App\Models\DamageProduct;
use App\Models\FloorRecieve;
use App\Models\PurchaseProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\RequisitionReceivedRequest;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    //Product stock list
    public function productStockList(Request $request)
    {
        $categories = Category::select('id', 'name')->get();
        $fromDate = $request->query('fromDate');
        $toDate = $request->query('toDate');
        $fd = $fromDate ? date('Y-m-d', strtotime($fromDate)) : '';
        $td = $toDate ? date('Y-m-d', strtotime($toDate)) : '';
        $categoryId = $request->query('category_id');
        $categoryName = '';

        if($categoryId){
            $categoryName = Category::where('id', $categoryId)->first()->name;
        }


        $products = Product::with('category')
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })->get();



        // Calculate total received, issue
        $receivedSums = RequisitionReceivedRequest::when($fd && $td, function ($query) use ($fd, $td) {
            $query->whereDate('created_at', '>=', $fd)->whereDate('created_at', '<=', $td);
        })->where('status', 'approved')
            ->select('product_id', DB::raw('SUM(received_qty) as total_received'))
            ->groupBy('product_id')
            ->pluck('total_received', 'product_id');


        $issueSums = IssueProduct::when($fd && $td, function ($query) use ($fd, $td) {
            $query->whereDate('created_at', '>=', $fd)->whereDate('created_at', '<=', $td);
        })->select('product_id', DB::raw('SUM(unit) as total_issue'))
            ->groupBy('product_id')
            ->pluck('total_issue', 'product_id');

        $floorRecieveSums = FloorRecieve::when($fd && $td, function ($query) use ($fd, $td) {
            $query->whereDate('created_at', '>=', $fd)->whereDate('created_at', '<=', $td);
        })->where('status', 'approved')->select('product_id', DB::raw('SUM(unit) as total_floor_recieve'))
            ->groupBy('product_id')
            ->pluck('total_floor_recieve', 'product_id');


        // Merge data
        $productList = [];

        foreach ($products as $product) {
            $totalReceived = $receivedSums[$product->id] ?? 0;
            $totalIssue = $issueSums[$product->id] ?? 0;
            $floorRecieve = $floorRecieveSums[$product->id] ?? 0;
            if ($fromDate && $toDate) {
                if ($totalReceived == 0 && $totalIssue == 0 && $floorRecieve == 0) {
                    continue;
                }
            }
            $productList[] = [
                'available_unit' => $product->unit,
                'unit_type' => $product->unit_type,
                'category_name' => $product->category->name,
                'parts_no' => $product->parts_no,
                'rack_no' => $product->rack_no,
                'column_no' => $product->column_no,
                'row_no' => $product->row_no,
                'product_name' => $product->name,
                'total_received' => $totalReceived,
                'floor_recieve' => $floorRecieve,
                'total_issue' => $totalIssue,
            ];
        }

        return Inertia::render('Products/ProductStockListPage', [
            'productList' => $productList,
            'categories' => $categories,
            'category_name' => $categoryName
        ]);
    }


    //list product
    public function listProduct()
    {
        $products = Product::with('category')->select('image','id', 'category_id', 'name', 'parts_no', 'rack_no', 'column_no', 'row_no', 'unit', 'unit_type','brand_name')
        ->latest()->get();
        return Inertia::render('Products/ProductListPage', ['products' => $products]);
    }

    //product save page
    public function productSavePage(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::where('id', $productId)->first();
        $category = Category::all();
        return Inertia::render('Products/ProductSavePage', ['product' => $product, 'category' => $category]);
    }



    // product issue page
    public function productIssuePage(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::where('id', $productId)->first();
        return Inertia::render('Products/ProductDamageIssuePage', ['product' => $product]);
    }

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

    //minimum stock list
    public function minimumProductList(Request $request)
    {

        $products = Product::whereColumn('unit', '<=', 'minimum_stock')->with('category')->select('id', 'category_id', 'name', 'parts_no', 'rack_no', 'column_no', 'row_no', 'unit', 'unit_type','brand_name')->latest()->get();
        return Inertia::render('Products/MinimumStockListPage', ['products' => $products]);
    }

    //floor recieve list
    public function floorReceiveList(Request $request)
    {
        $floorRecieves = FloorRecieve::where('status', 'pending')->with('product')->get();
        return Inertia::render('FloorRecieve/FloorRecieveListPage', ['floorRecieves' => $floorRecieves]);
    }
    //floor recieve page
    public function floorRecievePage(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::where('id', $productId)->first();
        return Inertia::render('FloorRecieve/FloorRecieveSavePage', ['product' => $product]);
    }

    //floor receive
    public function floorReceive(Request $request)
    {
        $validator = validator::make($request->all(), [
            'floor_recieve' => 'required|numeric|min:1'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }
        try {
            FloorRecieve::create([
                'product_id' => $request->product_id,
                'unit' => $request->floor_recieve
            ]);
            return redirect()->back()->with(['status' => true, 'message' => 'Sent floor received request successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }

    //approve floor recieve
    public function approveFloorRecieve(Request $request)
    {
        try {
            $floorRecieve = FloorRecieve::findOrFail($request->received_id);
            $product = Product::findOrFail($floorRecieve->product_id);
            FloorRecieve::where('id', $request->received_id)->update([
                'status' => $request->status
            ]);
            if ($request->status == 'approved') {
                $product->increment('unit', $floorRecieve->unit);
                return redirect()->back()->with(['status' => true, 'message' => 'Floor recieve approved successfully']);
            } else {
                return redirect()->back()->with(['status' => true, 'message' => 'Floor recieve rejected successfully']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong' ]);
        }
    }

    //edit floor recieve page
    public function editFloorRecievePage(Request $request)
    {
        $floorRecieve = FloorRecieve::where('id', $request->id)->with('product')->first();
        return Inertia::render('FloorRecieve/EditFloorRecievePage', ['floorRecieve' => $floorRecieve]);
    }

    //update floor recieve
    public function updateFloorReceive(Request $request)
    {
        $validator = validator::make($request->all(), [
            'floor_recieve' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }
        try {
            FloorRecieve::where('id', $request->floor_recieve_id)->update([
                'unit' => $request->floor_recieve
            ]);
            return redirect()->back()->with(['status' => true, 'message' => 'Floor recieve updated successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
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
        })->with('product')->select('id','product_id','unit','category_id','machine_name','created_at')->latest()->paginate(1000);

        return Inertia::render('Products/ProductIssuePage', ['issueProducts' => $issueProducts, 'fromDate' => $fromDate, 'toDate' => $toDate]);
    }


    //damage product list
    public function damageProductList(Request $request)
    {
        $fromDate = $request->query('fromDate');
        $toDate = $request->query('toDate');

        $damageProducts = DamageProduct::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $fd = date('Y-m-d', strtotime($fromDate));
            $td = date('Y-m-d', strtotime($toDate));

            $query->whereDate('created_at', '>=', $fd)
                ->whereDate('created_at', '<=', $td);
        })->with('product')->select('id','product_id','unit','created_at')->latest()->paginate(1000);
        return Inertia::render('Products/DamageProductPage', ['damageProducts' => $damageProducts]);
    }

    //create product
    public function createProduct(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'unit' => 'required|numeric|min:1',
        'unit_type' => 'required',
        'category_id' => 'required',
        'minimum_stock' => 'required|numeric|min:1',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:100',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->with(['errors' => $validator->errors()]);
    }

    try {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'unit_type' => $request->unit_type,
            'minimum_stock' => $request->minimum_stock,
            'parts_no' => $request->parts_no,
            'rack_no' => $request->rack_no,
            'column_no' => $request->column_no,
            'row_no' => $request->row_no,
            'brand_name' => $request->brand_name
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $fileName);
            $data['image'] = $fileName; // ✅ Save only filename
        }

        Product::create($data);
        return redirect()->back()->with(['status' => true, 'message' => 'Product created successfully']);
    } catch (Exception $e) {
        return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
    }
}


    //update product
    public function updateProduct(Request $request)
    {
        // return $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'unit' => 'required|numeric|min:1',
            'unit_type' => 'required',
            'category_id' => 'required',
            'minimum_stock' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }

        try {
            $product = Product::findOrFail($request->product_id); // ✅ Get existing product

            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'unit' => $request->unit,
                'unit_type' => $request->unit_type,
                'minimum_stock' => $request->minimum_stock,
                'parts_no' => $request->parts_no,
                'rack_no' => $request->rack_no,
                'column_no' => $request->column_no,
                'row_no' => $request->row_no,
                'brand_name' => $request->brand_name
            ];

            if ($request->hasFile('image')) {

                $validator = Validator::make($request->all(), [

                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:100',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->with(['errors' => $validator->errors()]);
                }

                $image = $request->file('image');
                $fileName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads'), $fileName);
                $data['image'] = $fileName;


                $oldImage = $product->image;
                File::delete(public_path('uploads/'.$oldImage));

                $product->update($data);
                return redirect()->back()->with(['status' => true, 'message' => 'Product updated successfully']);
            }else{



                $oldImage = $product->image;
                if($oldImage != null && $request->image == null){

                    File::delete(public_path('uploads/'.$oldImage));
                    $data['image'] = null;
                    $product->update($data);
                    return redirect()->back()->with(['status' => true, 'message' => 'Product updated successfully']);
                }
                else{

                    $data['image'] = $request->image;
                     $product->update($data);
                     return redirect()->back()->with(['status' => true, 'message' => 'Product updated successfully']);
                }
            }

        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }




    //delete product
    public function deleteProduct(Request $request)
    {
        try {
            Product::where('id', $request->product_id)->delete();
            return redirect()->back()->with(['status' => true, 'message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }
}
