<?php

namespace App\Http\Controllers\Backend;

use Exception;
use App\Models\Product;
use App\Models\FloorRecieve;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FloorReceiveController extends Controller
{
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
            return redirect()->back()->with(['status' => false, 'message' => 'Something went wrong']);
        }
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
}
