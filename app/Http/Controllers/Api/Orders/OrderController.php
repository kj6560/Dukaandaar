<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Product;
use App\Models\ProductScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->groupBy(
                'orders.id',
                'orders.org_id',
                'orders.order_date',
                'orders.total_order_value',
                'orders.total_order_discount',
                'orders.net_order_value',
                'orders.order_status',
                'orders.tax',
                'orders.net_total',
                'orders.created_by'
            )
            ->select(
                'orders.id as order_id',
                'orders.org_id',
                'orders.order_date',
                'orders.total_order_value',
                'orders.total_order_discount',
                'orders.net_order_value',
                'orders.order_status',
                'orders.tax',
                'orders.net_total',
                'orders.created_by',
                DB::raw('
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        "order_detail_id", ord.id,
                        "product_id", ord.product_id,
                        "base_price", ord.base_price,
                        "discount", ord.discount,
                        "tax", ord.tax,
                        "net_price", ord.net_price,
                        "quantity", ord.quantity,
                        "product_name", p.name
                    )
                ) as order_details
            ')
            );

        if (!empty($request->order_id)) {
            $orders = $orders->where('orders.id', $request->order_id)->first();
            $orders->order_details = json_encode($orders->order_details);
        } else {
            $orders = $orders->get();
            foreach ($orders as $order) {
                $order = json_encode($order->order_details);
            }
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Orders fetched successfully',
            'data' => $orders
        ]);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
            'order' => 'required|array',
            'created_by' => 'required',
        ]);
        $order = $request->order;
        if (!is_array($order) || empty($order)) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Order details are required',
                'data' => []
            ]);
        }

        if (!empty($request->order_id)) {
            $order = Orders::find($request->order_id);
        } else {
            $order = new Orders();
        }
        $error = [];
        $total_order_value = 0;
        $total_order_discount = 0;
        $net_order_value = 0;
        $tax = 0;
        $net_total = 0;

        foreach ($request->order as $order_detail) {
            $_total_order_value = 0;
            $_total_order_discount = 0;

            $product = Product::where('sku', $order_detail['sku'])->first();
            if (empty($product->id)) {
                $error[] = $order_detail['sku']."Not found";
                continue;
            }
            $productInventory = Inventory::where('product_id', $product->id)->first();
            if(empty($productInventory->id)) {
                $error[] = $order_detail['sku']."No Inventory found";
            }
            if ($productInventory->quantity < $order_detail['quantity']) {
                $error[] = "Product quantity is not sufficiently available in inventory";
                continue;
            }
            
            $productSchemes = ProductScheme::where('product_id', $product->id)->get();
            if ($productSchemes->isNotEmpty()) {
                foreach ($productSchemes as $scheme) {
                    $bundle_products = json_decode($scheme->bundle_products, true);
                    if (is_array($bundle_products)) {
                        foreach ($bundle_products as $bundle_product) {
                            $bundle_product_details = Product::where('id', $bundle_product['product_id'])->first();
                            $bundleProductInventory = Inventory::where('product_id', $bundle_product['product_id'])->first();
                        
                            if(empty($bundleProductInventory->id)) {
                                $error[] = "No Inventory found for $bundle_product_details->sku";
                                continue;
                            }
                            if ($bundleProductInventory->balance_quantity < $bundle_product['quantity'] ) {
                                $error[] = "Bundle product quantity is not sufficiently available in inventory";
                                continue;
                            }
                            if (!empty($bundle_product_details)) {
                                switch ($scheme->type) {
                                    case 'combo':
                                        $_total_order_value += $product->product_mrp * $order_detail['quantity'] + $bundle_product_details->product_mrp * $bundle_product['quantity'] + $scheme->value;
                                        $_total_order_discount += $product->product_mrp * $order_detail['quantity'] + $bundle_product_details->product_mrp * $bundle_product['quantity'];
                                        break;
                                    case 'bogs':
                                        echo "bogs";
                                        break;
                                    case 'fixed_discount':
                                        echo "fixed_discount";
                                        // Handle cashback logic if needed
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                    }
                }
            } else {
                $_total_order_value += $product->product_mrp * $order_detail['quantity'];
                $_total_order_discount += $order_detail['discount'];
            }

            $total_order_value += $_total_order_value;
            $total_order_discount += $_total_order_discount;
            $net_order_value += $_total_order_value - $_total_order_discount;
            $tax += $order_detail['tax'];
            $net_total += $_total_order_value - $_total_order_discount + $tax;
        }
        if (!empty($error)) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Some products are not available',
                'data' => $error
            ]);
        }
        $order->org_id = $request->org_id;
        $order->order_date = date('Y-m-d H:i:s');
        $order->total_order_value = $total_order_value;
        $order->total_order_discount = $total_order_discount;
        $order->net_order_value = $net_order_value;
        $order->tax = $tax;
        $order->net_total = $net_total;
        $order->created_by = $request->created_by;
        $order->order_status = 1;
        if (!empty($request->customer_id)) {
            $order->customer_id = $request->customer_id;
        }
        if (!empty($request->payment_mode)) {
            $order->payment_mode = $request->payment_mode;
        }
        $order->save();

        if ($order->save()) {
            $count = 0;
            foreach ($request->order as $order_detail) {
                $count++;
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
                $orderDetail->base_price = $product->product_mrp;
                $orderDetail->discount = $order_detail['discount'];
                $orderDetail->tax = $order_detail['tax'];
                $orderDetail->net_price = $product->product_mrp * $order_detail['quantity'] - $order_detail['discount'] + $order_detail['tax'];
                $orderDetail->quantity = $order_detail['quantity'];
                $orderDetail->save();
            }
            if (count($request->order) != $count) {
                return response()->json([
                    'statusCode' => 400,
                    'message' => 'Some products are not available',
                    'data' => []
                ]);
            } else {
                //update inventory
                $inventory_updated_count = 0;
                foreach ($request->order as $order_detail) {
                    $product = Product::where('sku', $order_detail['sku'])->first();
                    $inventory_updated = $this->updateInventory($order_detail['quantity'], 'sale', $product->sku, $request->org_id);
                    if ($inventory_updated) {
                        $inventory_updated_count++;
                    }
                }
            }
        }
        $orders = Orders::join('order_details as ord', 'ord.order_id', '=', 'orders.id')
            ->join('products as p', 'p.id', '=', 'ord.product_id')
            ->where('orders.org_id', $request->org_id)
            ->groupBy(
                'orders.id',
                'orders.org_id',
                'orders.order_date',
                'orders.total_order_value',
                'orders.total_order_discount',
                'orders.net_order_value',
                'orders.order_status',
                'orders.tax',
                'orders.net_total',
                'orders.created_by'
            )
            ->select(
                'orders.id as order_id',
                'orders.org_id',
                'orders.order_date',
                'orders.total_order_value',
                'orders.total_order_discount',
                'orders.net_order_value',
                'orders.order_status',
                'orders.tax',
                'orders.net_total',
                'orders.created_by',
                DB::raw('
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        "order_detail_id", ord.id,
                        "product_id", ord.product_id,
                        "base_price", ord.base_price,
                        "discount", ord.discount,
                        "tax", ord.tax,
                        "net_price", ord.net_price,
                        "product_name", p.name
                    )
                ) as order_details
            ')
            )
            ->where('orders.id', $order->id)
            ->first();
        $orders->order_details = json_encode($orders->order_details);
        return response()->json([
            'statusCode' => 200,
            'message' => 'Order updated successfully',
            'data' => $orders
        ]);
    }
    public function updateInventory($quantity, $transaction_type, $sku, $org_id)
    {

        $product = Product::where('sku', $sku)->first();
        $inventory = Inventory::where('product_id', $product->id)->first();
        if (!empty($inventory->id)) {
            $old_quantity = $inventory->balance_quantity;
            if (!empty($inventory->balance_quantity)) {
                $inventory->old_quantity = $old_quantity;
            }
            if ($transaction_type == 'purchase') {
                $inventory->balance_quantity = $inventory->balance_quantity + $quantity;
            } else if ($transaction_type == 'sale') {
                $inventory->balance_quantity = $inventory->balance_quantity - $quantity;
            } else if ($transaction_type == 'adjustment') {
                $inventory->balance_quantity = $quantity;
            }
        } else {
            $inventory = new Inventory();
            $inventory->balance_quantity = $quantity;
            $inventory->is_active = 1;
        }
        $inventory->org_id = $org_id;
        $inventory->product_id = $product->id;
        $inventory->quantity = $quantity;
        if ($inventory->save()) {
            $transaction = new InventoryTransaction();
            $transaction->inventory_id = $inventory->id;
            $transaction->transaction_type = $transaction_type;
            $transaction->quantity = $quantity;
            $transaction->transaction_by = Auth::user()->id;
            if ($transaction->save()) {
                return true;
            } else {
                return false;
            }
        }
    }
}
