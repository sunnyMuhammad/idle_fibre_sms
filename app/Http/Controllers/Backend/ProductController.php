<?php

namespace App\Http\Controllers\Backend;

use Exception;
use League\Csv\Reader;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Services\ProductStockListService;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{

    //all products
   public function productList()
{
    $products = Product::with('category')
        ->select('id', 'image', 'name', 'description', 'category_id', 'brand_name', 'unit', 'unit_type', 'rack_no', 'column_no', 'row_no')
        ->orderBy('id', 'desc')
        ->get();

    return response()->json(['products' => $products]);
}


    // low stock
    public function lowStock(Request $request)
    {
        $minimumSotck = Product::whereColumn('unit', '<=', 'minimum_stock')->with('category')
            ->select('id', 'category_id', 'name', 'parts_no', 'rack_no', 'column_no', 'row_no', 'unit', 'unit_type', 'brand_name')
            ->orderBy('id', 'desc')->get();


        return response()->json([
            'minimumSotck' => $minimumSotck,
            'message' => 'success'
        ], 200);
    }


    //product stock list
    public function productStockList(ProductStockListService $productStockListService,Request $request)
    {


      $productStockList =  $productStockListService->productStockList($request);

        return response()->json([
            'productStockList' => $productStockList,
            'message' => 'success',
        ], 200);
    }


    //create product
    public function createProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'unit' => 'required|numeric|min:0',
            'unit_type' => 'required',
            'category_id' => 'required',
            'minimum_stock' => 'required|numeric|min:0',
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
                $fileName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('uploads', $fileName);
                $data['image'] = $fileName;
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

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'unit' => 'required|numeric|min:0',
            'unit_type' => 'required',
            'category_id' => 'required',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->errors()]);
        }

        try {
            $product = Product::findOrFail($request->product_id);

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
                $fileName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('uploads', $fileName);
                $data['image'] = $fileName;


                $oldImage = $product->image;
                Storage::disk('public')->delete('uploads/' . $oldImage);

                $product->update($data);
                return redirect()->back()->with(['status' => true, 'message' => 'Product updated successfully']);
            } else {

                $oldImage = $product->image;
                if ($oldImage != null && $request->image == null) {

                    Storage::disk('public')->delete('uploads/' . $oldImage);
                    $data['image'] = null;
                    $product->update($data);
                    return redirect()->back()->with(['status' => true, 'message' => 'Product updated successfully']);
                } else {

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
            $product = Product::findOrFail($request->product_id);
            $oldImage = $product->image;

            $product->delete();

            if ($oldImage != null) {
                Storage::disk('public')->delete('uploads/' . $oldImage);
            }


            return redirect()->back()->with(['status' => true, 'message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }
}
