<?php

namespace App\Http\Controllers\Backend;

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
use League\Csv\Reader;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{

    //create product
    public function createProduct(Request $request)
    {

        // $csv = Reader::createFromPath(public_path('products/electrical.csv'));
        // $reader = $csv->getRecords();
        // foreach ($reader as $row) {

        //     Product::create([
        //         'name' => $row[1],
        //         'unit' => is_numeric($row[3]) ? $row[3] : 0, // যদি সংখ্যা না হয়, তাহলে 0 দাও
        //         'unit_type' => $row[2],
        //         'category_id' => 9,
        //     ]);

        // }

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
                $image->move(public_path('uploads'), $fileName);
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
                $image->move(public_path('uploads'), $fileName);
                $data['image'] = $fileName;


                $oldImage = $product->image;
                File::delete(public_path('uploads/' . $oldImage));

                $product->update($data);
                return redirect()->back()->with(['status' => true, 'message' => 'Product updated successfully']);
            } else {

                $oldImage = $product->image;
                if ($oldImage != null && $request->image == null) {

                    File::delete(public_path('uploads/' . $oldImage));
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
                File::delete(public_path('uploads/' . $oldImage));
            }

            return redirect()->back()->with(['status' => true, 'message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => false, 'message' => 'somethintg went wrong']);
        }
    }
}
