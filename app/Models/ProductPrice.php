<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = 'product_price';

    public function uom()
    {
        return $this->belongsTo(ProductUom::class, 'uom_id');
    }
}
