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
                'product_name' => $product->name,
                'category_name' => $product->category->name,
                'parts_no' => $product->parts_no,
                'rack_no' => $product->rack_no,
                'column_no' => $product->column_no,
                'row_no' => $product->row_no,
                'floor_recieve' => $floorRecieve,
                'total_received' => $totalReceived,
                'total_issue' => $totalIssue,
                'available_unit' => $product->unit,
                'unit_type' => $product->unit_type,
            ];
        }

        return [
            'productList' => $productList,
            'categories' => $categories,
            'category_name' => $categoryName
        ];
    }
}
