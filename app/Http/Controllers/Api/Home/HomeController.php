<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function fetchKpi()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                "sales" => [
                    'sales_today' => Orders::where('created_at', '>=', date('Y-m-d 00:00:00'))->count(),
                    'sales_this_month' => Orders::where('created_at', '>=', date('Y-m-01 00:00:00'))->count(),
                    'sales_total' => Orders::count()
                    
                ],
                "inventory" => [
                    'inventory_added_today' => Inventory::where('created_at', '>=', date('Y-m-d 00:00:00'))->count(),
                    'inventory_added_this_month' => Inventory::where('created_at', '>=', date('Y-m-01 00:00:00'))->count(),
                    'inventory_added_total' => Inventory::count()
                ],
                "products" => [

                    'products_added_today' => Product::where('created_at', '>=', date('Y-m-d 00:00:00'))->count(),
                    'products_added_this_month' => Product::where('created_at', '>=', date('Y-m-01 00:00:00'))->count(),
                    'products_added_total' => Product::count()

                ]
            ]
        ]);
    }
}
