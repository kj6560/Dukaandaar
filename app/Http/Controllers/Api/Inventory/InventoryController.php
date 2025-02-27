<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function list(Request $request)
    {
        if (empty($request->org_id)) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Organization id is required',
                'data' => []
            ]);
        }
        $inventory = Inventory::join('products', 'products.id', '=', 'inventory.product_id')
            ->select(

                'inventory.id',
                'inventory.org_id',
                'inventory.product_id',
                'inventory.quantity',
                'inventory.balance_quantity',
                'products.name',
                'products.product_mrp',
                'inventory.is_active'
            )
            ->where('inventory.org_id', $request->org_id)->get();
        return response()->json(
            [
                'statusCode' => 200,
                'message' => 'Inventory list',
                'data' => $inventory
            ]
        );
    }
    public function updateInventory(Request $request)
    {
        $request->validate([
            'quantity' => 'required',
            'transaction_type' => 'required',
            'sku' => 'required',
            'org_id' => 'required'
        ]);
        $product = Product::where('sku', $request->sku)->first();
        if (!empty($request->id)) {
            $inventory = Inventory::find($request->id);
            if (!empty($inventory->id)) {
                $old_quantity = $inventory->balance_quantity;
                if (!empty($inventory->balance_quantity)) {
                    $inventory->old_quantity = $old_quantity;
                }
                if ($request->transaction_type == 'purchase') {
                    $inventory->balance_quantity = $inventory->balance_quantity + $request->quantity;
                } else if ($request->transaction_type == 'sale') {
                    $inventory->balance_quantity = $inventory->balance_quantity - $request->quantity;
                } else if ($request->transaction_type == 'adjustment') {
                    $inventory->balance_quantity = $request->quantity;
                }
            } else {
                $inventory = new Inventory();
                $inventory->balance_quantity = $request->quantity;
                $inventory->is_active = 1;
            }
        } else {
            $inventory = new Inventory();
            $inventory->balance_quantity = $request->quantity;
            $inventory->is_active = 1;
        }
        $inventory->org_id = $request->org_id;
        $inventory->product_id = $product->id;
        $inventory->quantity = $request->quantity;
        if ($inventory->save()) {
            $transaction = new InventoryTransaction();
            $transaction->inventory_id = $inventory->id;
            $transaction->transaction_type = $request->transaction_type;
            $transaction->transaction_by = Auth::user()->id;
            if ($transaction->save()) {
                $inventory->name = $product->name;
                $inventory->product_mrp = $product->product_mrp;
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Inventory updated successfully',
                    'data' => $inventory
                ]);
            } else {
                return response()->json([
                    'statusCode' => 400,
                    'message' => 'Inventory not updated',
                    'data' => []
                ]);
            }
        }
    }
}
