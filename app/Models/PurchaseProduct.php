<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseProduct extends Model
{
       protected $fillable = [
        'product_id',
        'vendor_id',
        'unit',
        'price',
        'reqisition_no',
        'brand_name',
        'remarks'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function vendor()  {
        return $this->belongsTo(Vendor::class);
    }

}
