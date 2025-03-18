<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductScheme extends Model {
    use HasFactory;

    protected $fillable = [
        'product_id','org_id', 'scheme_name', 'type', 'value', 
        'duration', 'bundle_products', 'start_date', 
        'end_date', 'is_active'
    ];

    protected $casts = [
        'bundle_products' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
