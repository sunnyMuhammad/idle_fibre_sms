<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\FloorRecieve;
use App\Models\IssueProduct;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionReceivedRequest;



class ProductStockListService
{
    //Product stock list
    public function productStockList($data)
    {
        $categories = Category::select('id', 'name')->get();
        $fromDate = $data->query('fromDate');
        $toDate = $data->query('toDate');
        $fd = $fromDate ? date('Y-m-d', strtotime($fromDate)) : '';
        $td = $toDate ? date('Y-m-d', strtotime($toDate)) : '';
        $categoryId = $data->query('category_id');
        $categoryName = '';

        if ($categoryId) {
            $categoryName = Category::where('id', $categoryId)->first();
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



        $productList = $products->filter(function ($product) use ($receivedSums, $issueSums, $floorRecieveSums, $fd, $td) {

            $id = $product->id;
            $total_received = $receivedSums[$id] ?? 0;
            $total_issue = $issueSums[$id] ?? 0;
            $total_floor_recieve = $floorRecieveSums[$id] ?? 0;

            if ($fd && $td) {
                return $total_received || $total_issue || $total_floor_recieve;
            }

            return true;
        })->map(function ($product) use ($receivedSums, $issueSums, $floorRecieveSums) {
            $id = $product->id;
            return [
                'product_name'    => $product->name,
                'category_name'   => $product->category->name,
                'parts_no'        => $product->parts_no,
                'rack_no'         => $product->rack_no,
                'column_no'       => $product->column_no,
                'row_no'          => $product->row_no,
                'floor_recieve'   => $floorRecieveSums[$id] ?? 0,
                'total_received'  => $receivedSums[$id] ?? 0,
                'total_issue'     => $issueSums[$id] ?? 0,
                'available_unit'  => $product->unit,
                'unit_type'       => $product->unit_type,
            ];
        })->values();



        return [
            'productList' => $productList,
            'categories' => $categories,
            'category_name' => $categoryName
        ];
    }
}
