<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\OrderDetails;
use App\Models\Orders;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ProductScheme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class OrderController extends Controller
{
    public function fetchOrders(Request $request)
    {
        if($this->checkSubscription(Auth::user()->org_id) == false){
            return response()->json([
                'statusCode' => 202,
                'message' => 'You don\'t have an active subscription. Plz contact admin',
                'data' => []
            ], 202);
        }
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
            ->orderBy('orders.id', 'desc')
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
            $organization = Organization::where('id',$org_id)->first();
            $orders->order_details = json_encode($orders->order_details);
            $orderDetails = is_string($orders->order_details)?? json_decode(json_decode($orders->order_details), true);
            $invoiceText = "       *** INVOICE ***       \n";
            $invoiceText .= "----------------------------\n";
            $invoiceText .= "Order ID: {$orders->order_id}\n";
            $invoiceText .= "Date: " . date('d M Y H:i', strtotime($orders->order_date)) . "\n";
            $invoiceText .= "----------------------------\n";
            $orderDetails = is_string($orders->order_details)
                ? json_decode(json_decode($orders->order_details), true)
                : $orders->order_details;
            $invoiceText .= "Organization: " . ($organization->org_name ?? 'Customer') . "\n";
            $invoiceText .= "----------------------------\n";
            $invoiceText .= "Customer: " . ($orders->customer_name ?? 'Customer') . "\n";
            $invoiceText .= "Order ID: {$orders->order_id}\n";
            $invoiceText .= "----------------------------\n";

            foreach ($orderDetails as $item) {
                $invoiceText .= "{$item['product_name']} x{$item['quantity']}\n";
                $invoiceText .= "Price: ₹" . number_format($item['base_price'], 2) . "\n";
                $invoiceText .= "Discount: ₹" . number_format($item['discount'], 2) . "\n";
                $invoiceText .= "Tax: ₹" . number_format($item['tax'], 2) . "\n";
                $invoiceText .= "Net: ₹" . number_format($item['net_price'], 2) . "\n";
                $invoiceText .= "----------------------------\n";
            }

            $invoiceText .= "Order Total: ₹" . number_format($orders->total_order_value, 2) . "\n";
            $invoiceText .= "Discount: ₹" . number_format($orders->total_order_discount, 2) . "\n";
            $invoiceText .= "Tax: ₹" . number_format($orders->tax, 2) . "\n";
            $invoiceText .= "Net Total: ₹" . number_format($orders->net_total, 2) . "\n";
            $invoiceText .= "----------------------------\n";
            $invoiceText .= "Thank you for your purchase!\n";
            $invoiceText .= "----------------------------\n";
            $invoiceText .= "                            \n";
            $invoiceText .= "----------------------------\n";
            // $orders->print_invoice = View::make('invoice.invoice_template_1', [
            //     'order' => $orders,
            //     'orderDetails' => $orderDetails,
            //     'organization' => $organization,
            // ])->render();
            $orders->print_invoice = $invoiceText;
        } else {
            $orders = $orders->get();
            foreach ($orders as $order) {
                $order->order_details = json_encode($order->order_details);
                $orderDetails = is_string($order->order_details)
                    ? json_decode(json_decode($order->order_details), true)
                    : $orders->order_details;

                $invoiceText = "INVOICE\n";
                $invoiceText .= "----------------------------\n";
                $invoiceText .= "Customer: {$order->customer_name}\n";
                $invoiceText .= "Order ID: {$order->order_id}\n";
                $invoiceText .= "----------------------------\n";

                foreach ($orderDetails as $item) {
                    $invoiceText .= "{$item['product_name']} x{$item['quantity']}\n";
                    $invoiceText .= "Price: ₹" . number_format($item['base_price'], 2) . "\n";
                    $invoiceText .= "Discount: ₹" . number_format($item['discount'], 2) . "\n";
                    $invoiceText .= "Tax: ₹" . number_format($item['tax'], 2) . "\n";
                    $invoiceText .= "Net: ₹" . number_format($item['net_price'], 2) . "\n";
                    $invoiceText .= "----------------------------\n";
                }

                $invoiceText .= "Order Total: ₹" . number_format($order->total_order_value, 2) . "\n";
                $invoiceText .= "Discount: ₹" . number_format($order->total_order_discount, 2) . "\n";
                $invoiceText .= "Tax: ₹" . number_format($order->tax, 2) . "\n";
                $invoiceText .= "Net Total: ₹" . number_format($order->net_total, 2) . "\n";
                $invoiceText .= "----------------------------\n";
                $invoiceText .= "Thank you for your purchase!\n";
                $invoiceText .= "----------------------------\n";

                $order->print_invoice = $invoiceText;
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

        if (!is_array($request->order) || empty($request->order)) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Order details are required',
                'data' => []
            ]);
        }

        $order = !empty($request->order_id) ? Orders::find($request->order_id) : new Orders();
        $errors = [];

        $total_order_value = 0;
        $total_order_discount = 0;
        $net_order_value = 0;
        $tax = 0;
        $net_total = 0;

        foreach ($request->order as $order_detail) {
            if (!isset($order_detail['sku'], $order_detail['quantity'], $order_detail['tax'], $order_detail['discount'])) {
                $errors[] = "Missing fields in order detail";
                continue;
            }

            $sku = $order_detail['sku'];
            $quantity = $order_detail['quantity'];
            $orderTax = $order_detail['tax'];
            $discount = $order_detail['discount'];

            $_total_order_value = 0;
            $_total_order_discount = 0;

            $product = Product::where('sku', $sku)->first();
            if (!$product) {
                $errors[] = "$sku not found";
                continue;
            }

            $inventory = Inventory::where('product_id', $product->id)->first();
            if (!$inventory || $inventory->balance_quantity < $quantity) {
                $errors[] = "Insufficient inventory for $sku";
                continue;
            }

            $productSchemes = ProductScheme::where('product_id', $product->id)->get();
            if ($productSchemes->isNotEmpty()) {
                foreach ($productSchemes as $scheme) {
                    $bundle_products = json_decode($scheme->bundle_products, true);
                    if (is_array($bundle_products)) {
                        foreach ($bundle_products as $bundle_product) {
                            if (!isset($bundle_product['product_id'], $bundle_product['quantity'])) continue;

                            $bundleProduct = Product::find($bundle_product['product_id']);
                            $bundleInventory = Inventory::where('product_id', $bundle_product['product_id'])->first();

                            if (!$bundleInventory || $bundleInventory->balance_quantity < $bundle_product['quantity']) {
                                $errors[] = "Insufficient bundle inventory for SKU: {$bundleProduct->sku}";
                                continue;
                            }

                            switch ($scheme->type) {
                                case 'combo':
                                case 'bogs':
                                    $_total_order_value += $product->product_mrp * $quantity + $bundleProduct->product_mrp * $bundle_product['quantity'] + $scheme->value;
                                    $_total_order_discount += $product->product_mrp * $quantity + $bundleProduct->product_mrp * $bundle_product['quantity'];
                                    break;
                                case 'fixed_discount':
                                    $_total_order_value += $product->product_mrp * $quantity;
                                    $_total_order_discount += ($product->product_mrp * $quantity) - ($bundleProduct->product_mrp * $bundle_product['quantity']) * ($scheme->value / 100);
                                    break;
                            }
                        }
                    }
                }
            } else {
                $_total_order_value += $product->product_mrp * $quantity;
                $_total_order_discount += $discount;
            }

            $total_order_value += $_total_order_value;
            $total_order_discount += $_total_order_discount;
            $net_order_value += $_total_order_value - $_total_order_discount;
            $tax += $orderTax;
            $net_total += ($_total_order_value - $_total_order_discount + $orderTax);
        }

        if (!empty($errors)) {
            return response()->json([
                'statusCode' => 400,
                'message' => $errors,
                'data' => []
            ]);
        }

        $order->org_id = $request->org_id;
        $order->order_date = now();
        $order->total_order_value = $total_order_value;
        $order->total_order_discount = $total_order_discount;
        $order->net_order_value = $net_order_value;
        $order->tax = $tax;
        $order->net_total = $net_total;
        $order->created_by = $request->created_by;
        $order->order_status = 1;
        $order->customer_id = $request->customer_id ?? null;
        $order->payment_mode = $request->payment_mode ?? null;
        $order->save();

        $count = 0;
        foreach ($request->order as $order_detail) {
            $product = Product::where('sku', $order_detail['sku'])->first();
            if (!$product) continue;

            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $product->id;
            $orderDetail->base_price = $product->product_mrp;
            $orderDetail->discount = $order_detail['discount'];
            $orderDetail->tax = $order_detail['tax'];
            $orderDetail->quantity = $order_detail['quantity'];
            $orderDetail->net_price = $product->product_mrp * $order_detail['quantity'] - $order_detail['discount'] + $order_detail['tax'];
            $orderDetail->save();

            $count++;
        }

        if (count($request->order) != $count) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Some products are not available',
                'data' => []
            ]);
        }

        // Update inventory
        foreach ($request->order as $order_detail) {
            $product = Product::where('sku', $order_detail['sku'])->first();
            if ($product) {
                $this->updateInventory($order_detail['quantity'], 'sale', $product->sku, $request->org_id);
            }
        }

        $orders = Orders::join('order_details as ord', 'ord.order_id', '=', 'orders.id')
            ->join('products as p', 'p.id', '=', 'ord.product_id')
            ->where('orders.org_id', $request->org_id)
            ->where('orders.id', $order->id)
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
                DB::raw('JSON_ARRAYAGG(
                JSON_OBJECT(
                    "order_detail_id", ord.id,
                    "product_id", ord.product_id,
                    "base_price", ord.base_price,
                    "discount", ord.discount,
                    "tax", ord.tax,
                    "quantity", ord.quantity,
                    "net_price", ord.net_price,
                    "product_name", p.name
                )
            ) as order_details')
            )
            ->first();

        // Generate invoice
        $invoiceDir = 'invoices';

        $user = User::find($request->created_by);
        $org = Organization::find($user->org_id);
        $pdf = Pdf::loadView('orders.invoice', ['order' => $orders, 'org' => $org]);
        $invoiceFileName = 'invoice_' . $user->org_id . '_' . $orders->order_id . '.pdf';
        $pdfPath = $invoiceDir . '/' . $invoiceFileName;
        $pdf->save(storage_path('app/public/' . $pdfPath));
        $invoiceUrl = url('invoices/' . $invoiceFileName);

        $orders->order_details = json_encode($orders->order_details);

        $invoiceText = "       *** INVOICE ***       \n";
        $invoiceText .= "----------------------------\n";
        $invoiceText .= "Order ID: {$orders->order_id}\n";
        $invoiceText .= "Date: " . date('d M Y H:i', strtotime($orders->order_date)) . "\n";
        $invoiceText .= "----------------------------\n";


        $orderDetails = is_string($orders->order_details)
            ? json_decode(json_decode($orders->order_details), true)
            : $orders->order_details;

        $invoiceText = "INVOICE\n";
        $invoiceText .= "----------------------------\n";
        $invoiceText .= "Customer: {$orders->customer_name}\n";
        $invoiceText .= "Order ID: {$orders->order_id}\n";
        $invoiceText .= "----------------------------\n";

        foreach ($orderDetails as $item) {
            $invoiceText .= "{$item['product_name']} x{$item['quantity']}\n";
            $invoiceText .= "Price: ₹" . number_format($item['base_price'], 2) . "\n";
            $invoiceText .= "Discount: ₹" . number_format($item['discount'], 2) . "\n";
            $invoiceText .= "Tax: ₹" . number_format($item['tax'], 2) . "\n";
            $invoiceText .= "Net: ₹" . number_format($item['net_price'], 2) . "\n";
            $invoiceText .= "----------------------------\n";
        }

        $invoiceText .= "Order Total: ₹" . number_format($orders->total_order_value, 2) . "\n";
        $invoiceText .= "Discount: ₹" . number_format($orders->total_order_discount, 2) . "\n";
        $invoiceText .= "Tax: ₹" . number_format($orders->tax, 2) . "\n";
        $invoiceText .= "Net Total: ₹" . number_format($orders->net_total, 2) . "\n";
        $invoiceText .= "----------------------------\n";
        $invoiceText .= "Thank you for your purchase!\n";
        $invoiceText .= "----------------------------\n";

        $orders->print_invoice = $invoiceText;

        return response()->json([
            'statusCode' => 200,
            'message' => 'Order updated successfully',
            'data' => $orders
        ]);
    }

    public function updateInventory($quantity, $transaction_type, $sku, $org_id)
    {

        $product = Product::where('sku', $sku)->first();
        if (empty($product->id)) {
            return false;
        }
        $mainProductInventory  = $this->saveInventory($quantity, $transaction_type, $org_id, $product->id);
        if ($mainProductInventory) {
            $productSchemes = ProductScheme::where('product_id', $product->id)->get();
            if ($productSchemes->isNotEmpty()) {
                foreach ($productSchemes as $scheme) {
                    $bundle_products = json_decode($scheme->bundle_products, true);
                    if (is_array($bundle_products)) {
                        foreach ($bundle_products as $bundle_product) {
                            $this->saveInventory($bundle_product['quantity'], $transaction_type, $org_id, $bundle_product['product_id']);
                        }
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
    public function saveInventory($quantity, $transaction_type, $org_id, $product_id)
    {
        $inventory = Inventory::where('product_id', $product_id)->first();
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
        $inventory->product_id = $product_id;
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
    public function fetchCustomerOrders(Request $request)
    {
        $request->validate([
            'org_id' => 'required'
        ]);
        $orders = Orders::with('orderDetails', 'orderDetails.product')
            ->where('org_id', $request->org_id);
        if (!empty($request->customer_id)) {
            $orders = $orders->where('customer_id', $request->customer_id);
        }
        $orders = $orders->orderBy('orders.id', 'desc')->get();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Orders fetched successfully',
            'data' => $orders
        ]);
    }
}
