<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'category_id', 
        'unit', 
        'unit_type',
        'minimum_stock',
        'parts_no',
        'rack_no',
        'column_no',
        'row_no',
        'brand_name',
        'image',
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }
}
