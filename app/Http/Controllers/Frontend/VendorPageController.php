<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VendorPageController extends Controller
{
    //list vendor
    public function listVendor()
    {
        $vendors = Vendor::all();
        return Inertia::render('Vendor/VendorListPage', ['vendors' => $vendors]);
    }

    //vendor save page
    public function vendorSavePage(Request $request)
    {
        $vendor = Vendor::find($request->vendor_id);
        return Inertia::render('Vendor/VendorSavePage', ['vendor' => $vendor]);
    }
}
