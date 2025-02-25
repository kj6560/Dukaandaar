<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function fetchKpi()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                "sales" => [
                    'sales_today' => 100,
                    'sales_this_month' => 200,
                    'sales_total' => 300
                ],
                "inventory"=>[
                    'inventory_added_today' => 100,
                    'inventory_added_this_month' => 200,
                    'inventory_added_total' => 300
                ],
                "products"=>[
                    'products_added_today' => 100,
                    'products_added_this_month' => 200,
                    'products_added_total' => 300
                ]
            ]
        ]);
    }
}
