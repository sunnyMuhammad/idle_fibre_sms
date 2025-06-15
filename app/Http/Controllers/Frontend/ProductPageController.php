<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\DamageProduct;
use App\Http\Controllers\Controller;
use App\Services\ProductStockListService;

class ProductPageController extends Controller
{

    protected $productStockList;
    public function __construct(ProductStockListService $productStockListService)
    {
        $this->productStockList = $productStockListService;
    }

    //product stock list
    public function productStockList(Request $request)
    {

        $productStockList = $this->productStockList->productStockList($request);
        return Inertia::render('Products/ProductStockListPage', [
            'productList' => $productStockList['productList'],
            'categories' => $productStockList['categories'],
            'category_name' => $productStockList['category_name'],
        ]);
    }

    //  product stock report
    public function productStockReport(Request $request)
    {

        $productStockList = $this->productStockList->productStockList($request);
        $filePath = 'report/StockReport.csv';
        $file = fopen($filePath, 'w');
        fputcsv($file, array('Product Name', 'Category', 'Parts No', 'Rack No', 'Column No', 'Row No', 'Floor receive', 'Total received', 'Total issue', 'Available unit', 'Unit type'));
        foreach ($productStockList['productList'] as $key => $product) {
            fputcsv($file, $product);
        }
        return response()->download($filePath);
    }


    //list product
    public function listProduct()
    {
        $products = Product::with('category')->select('image', 'id', 'category_id', 'name', 'parts_no', 'rack_no', 'column_no', 'row_no', 'unit', 'unit_type', 'brand_name')
            ->orderBy('id', 'desc')->get();
        return Inertia::render('Products/ProductListPage', ['products' => $products]);
    }

    // product list report
    public function productListReport(Request $request)
    {

        $products = Product::with('category')->select('image', 'id', 'category_id', 'name', 'parts_no', 'rack_no', 'column_no', 'row_no', 'unit', 'unit_type', 'brand_name')
            ->latest()->get();
        $filePath = 'report/ProductReport.csv';
        $file = fopen($filePath, 'w');
        fputcsv($file, array('Product Name', 'Category', 'Parts No', 'Rack No', 'Column No', 'Row no', 'Available unit', 'Unit type', 'Brand Name'));
        foreach ($products as $key => $product) {
            $row = [
                $product->name,
                $product->category->name,
                $product->parts_no,
                $product->rack_no,
                $product->column_no,
                $product->row_no,
                $product->unit,
                $product->unit_type,
                $product->brand_name
            ];
            fputcsv($file, $row);
        }
        return response()->download($filePath);
    }

    //product save page
    public function productSavePage(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::where('id', $productId)->first();
        $category = Category::all();
        return Inertia::render('Products/ProductSavePage', ['product' => $product, 'category' => $category]);
    }

    //minimum stock list
    public function minimumProductList(Request $request)
    {

        $products = Product::whereColumn('unit', '<=', 'minimum_stock')->with('category')->select('id', 'category_id', 'name', 'parts_no', 'rack_no', 'column_no', 'row_no', 'unit', 'unit_type', 'brand_name')->latest()->get();
        return Inertia::render('Products/MinimumStockListPage', ['products' => $products]);
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
        })->with('product')->select('id', 'product_id', 'unit', 'created_at')->latest()->paginate(1000);
        return Inertia::render('Products/DamageProductPage', ['damageProducts' => $damageProducts]);
    }
}
