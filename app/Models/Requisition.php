<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $fillable=[
        'created_by',
    ];

    public function requisitionProducts(){
        return $this->hasMany(RequisitionProduct::class);
    }
}
