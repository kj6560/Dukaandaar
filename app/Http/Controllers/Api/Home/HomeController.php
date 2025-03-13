<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function fetchKpi()
    {
        if (!$this->checkSubscription(Auth::user()->id)) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    "sales_data" => [
                        'sales_today' => "NA",
                        'sales_this_month' => "NA",
                        'sales_total' => "NA"

                    ],
                    "inventory_data" => [
                        'inventory_added_today' => "NA",
                        'inventory_added_this_month' => "NA",
                        'inventory_added_total' => "NA"
                    ],
                    "products_data" => [

                        'products_added_today' => "NA",
                        'products_added_this_month' => "NA",
                        'products_added_total' => "NA"

                    ]
                ]
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                "sales_data" => [
                    'sales_today' => Orders::select(DB::raw("sum(orders.net_total) as sales_today"))->where('created_at', '>=', date('Y-m-d 00:00:00'))->first()->sales_today ?? "NA",
                    'sales_this_month' => Orders::select(DB::raw("sum(orders.net_total) as sales_this_month"))->where('created_at', '>=', date('Y-m-01 00:00:00'))->first()->sales_this_month ?? "NA",
                    'sales_total' => Orders::select(DB::raw("sum(orders.net_total) as sales_total"))->first()->sales_total ?? "NA"

                ],
                "inventory_data" => [
                    'inventory_added_today' => Inventory::where('created_at', '>=', date('Y-m-d 00:00:00'))->count(),
                    'inventory_added_this_month' => Inventory::where('created_at', '>=', date('Y-m-01 00:00:00'))->count(),
                    'inventory_added_total' => Inventory::count()
                ],
                "products_data" => [

                    'products_added_today' => Product::where('created_at', '>=', date('Y-m-d 00:00:00'))->count(),
                    'products_added_this_month' => Product::where('created_at', '>=', date('Y-m-01 00:00:00'))->count(),
                    'products_added_total' => Product::count()

                ]
            ]
        ]);
    }
}
