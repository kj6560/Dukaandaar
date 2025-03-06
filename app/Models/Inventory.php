<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public $table = "inventory";
    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function transactions() {
        return $this->hasMany(InventoryTransaction::class, 'inventory_id');
    }
}
