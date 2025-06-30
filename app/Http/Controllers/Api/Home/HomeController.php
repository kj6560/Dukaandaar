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
    public function fetchKpi(Request $request)
    {
        if (!$this->checkSubscription(Auth::user()->org_id)) {
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

                    ],
                    "cumulative_daily_order_report" => [],
                    "cumulative_monthly_order_report" => [],
                    "cumulative_yearly_order_report" => []
                ]
            ], 200);
        }
        $user = Auth::user();

        if ($user->device_id == $request->device_id) {
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

                    ],
                    "cumulative_daily_order_report" => $this->getDailyOrderReport(),
                    "cumulative_monthly_order_report" => $this->getMonthlyOrderReport(),
                    "cumulative_yearly_order_report" => $this->getYearlyOrderReport()
                ]
            ]);
        } else {
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
            ], 401);
        }
    }
    public function getDailyOrderReport()
    {
        return DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->selectRaw('
            DATE(orders.order_date) as report_date,
            COUNT(DISTINCT orders.id) as total_orders,
            SUM(orders.total_order_value) as total_order_value,
            SUM(orders.total_order_discount) as total_discount,
            SUM(orders.tax) as total_tax,
            SUM(orders.net_order_value) as net_order_value,
            SUM(order_details.quantity) as total_quantity
        ')
            ->groupBy(DB::raw('DATE(orders.order_date)'))
            ->orderBy('report_date', 'asc')
            ->get();
    }
    public function getMonthlyOrderReport()
    {
        return DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->selectRaw('
            DATE_FORMAT(orders.order_date, "%Y-%m") as report_month,
            COUNT(DISTINCT orders.id) as total_orders,
            SUM(orders.total_order_value) as total_order_value,
            SUM(orders.total_order_discount) as total_discount,
            SUM(orders.tax) as total_tax,
            SUM(orders.net_order_value) as net_order_value,
            SUM(order_details.quantity) as total_quantity
        ')
            ->groupBy(DB::raw('DATE_FORMAT(orders.order_date, "%Y-%m")'))
            ->orderBy('report_month', 'asc')
            ->get();
    }
    public function getYearlyOrderReport()
    {
        return DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->selectRaw('
            YEAR(orders.order_date) as report_year,
            COUNT(DISTINCT orders.id) as total_orders,
            SUM(orders.total_order_value) as total_order_value,
            SUM(orders.total_order_discount) as total_discount,
            SUM(orders.tax) as total_tax,
            SUM(orders.net_order_value) as net_order_value,
            SUM(order_details.quantity) as total_quantity
        ')
            ->groupBy(DB::raw('YEAR(orders.order_date)'))
            ->orderBy('report_year', 'asc')
            ->get();
    }
}
