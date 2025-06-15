<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryPageController extends Controller
{
    //list category
    public function listCategory()
    {
        $categories = Category::all();
        return Inertia::render('Category/CategoryListPage', ['categories' => $categories]);
    }

    //category save page
    public function categorySavePage(Request $request)
    {
        $categoryId = $request->category_id;
        $category = Category::where('id', $categoryId)->first();
        return Inertia::render('Category/CategorySavePage', ['category' => $category]);
    }
}
