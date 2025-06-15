<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\FloorRecieve;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FloorReceivePageController extends Controller
{
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

    //edit floor recieve page
    public function editFloorRecievePage(Request $request)
    {
        $floorRecieve = FloorRecieve::where('id', $request->id)->with('product')->first();
        return Inertia::render('FloorRecieve/EditFloorRecievePage', ['floorRecieve' => $floorRecieve]);
    }
}
