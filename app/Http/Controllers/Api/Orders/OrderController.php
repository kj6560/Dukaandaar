<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function fetchOrders(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
        ]);

        $org_id = $request->org_id;
        $orders = Orders::join('order_details as ord', 'ord.order_id', '=', 'orders.id')
            ->join('products as p', 'p.id', '=', 'ord.product_id')
            ->where('orders.org_id', $org_id)
            ->select(
                'orders.id as order_id',
                'orders.order_date',
                'orders.total_order_value',
                'orders.total_order_discount',
                'orders.net_order_value',
                'orders.order_status',
                'orders.tax',
                'orders.net_total',
                'orders.created_by',
                'ord.id as order_detail_id',
                'ord.product_id',
                'ord.base_price',
                'ord.discount',
                'ord.tax as product_tax',
                'ord.net_price',
                'p.name as product_name'
            )
            ->get()
            ->groupBy('order_id'); // Grouping by order_id
        return response()->json([
            'statusCode' => 200,
            'message' => 'orders fetched successfully',
            'data' => $orders
        ]);
    }
    public function updateOrder(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
            'order_date' => 'required',
            'total_order_value' => 'required',
            'total_order_discount' => 'required',
            'net_order_value' => 'required',
            'order_status' => 'required',
            'tax' => 'required',
            'net_total' => 'required',
            'created_by' => 'required'
        ]);
        if (!empty($request->order_id)) {
            $order = Orders::find($request->order_id);
        } else {
            $order = new Orders();
        }
        $order->org_id = $request->org_id;
        $order->order_date = $request->order_date;
        $order->total_order_value = $request->total_order_value;
        $order->total_order_discount = $request->total_order_discount;
        $order->net_order_value = $request->net_order_value;
        $order->tax = $request->tax;
        $order->net_total = $request->net_total;
        $order->created_by = $request->created_by;
        $order->order_status = $request->order_status;
        if ($order->save()) {
            $details = $request->details;
            foreach ($details as $order_detail) {
                
                $product = Product::where('sku', $order_detail['sku'])->first();

                if (empty($product->id)) {
                    continue;
                }
                if (!empty($request->order_id)) {
                    $orderDetail = OrderDetails::where('order_id', $request->order_id)->first();
                } else {
                    $orderDetail = new OrderDetails();
                }


                $orderDetail->order_id = $order->id;
                $orderDetail->product_id = $product->id;
                $orderDetail->base_price = $order_detail['base_price'];
                $orderDetail->discount = $order_detail['discount'];
                $orderDetail->tax = $order_detail['tax'];
                $orderDetail->net_price = $order_detail['net_price'];
                $orderDetail->quantity = $order_detail['quantity'];
                $orderDetail->save();
            }
        }
        return response()->json([
            'statusCode' => 200,
            'message' => 'Order updated successfully',
            'data' => $order
        ]);
    }
}
