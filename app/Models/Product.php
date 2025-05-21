<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public function prices()
    {
        return $this->hasMany(ProductPrice::class, 'product_id')->where('is_active', 1);
    }

    public function latestPrice()
    {
        return $this->hasOne(ProductPrice::class, 'product_id')->latest();
    }

    public function uom()
    {
        return $this->hasOneThrough(ProductUom::class, ProductPrice::class, 'product_id', 'id', 'id', 'uom_id')
            ->where('product_uom.is_active', 1);
    }
    
    public function schemes()
    {
        return $this->hasMany(ProductScheme::class, 'product_id');
    }
}
