<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function addProduct(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
            'name' => 'required',
            'product_mrp' => 'required',
            'sku' => 'required'
        ]);
        $product = Product::where('org_id', $request->org_id)->where('sku', $request->sku)->first();
        if ($product) {
            return response()->json(['statusCode' => 400, 'message' => 'Product already exists', 'data' => []], 400);
        }
        $product = new Product();
        $product->org_id = $request->org_id;
        $product->name = $request->name;
        $product->product_mrp = $request->product_mrp;
        $product->sku = $request->sku;
        $product->is_active = 1;
        if ($product->save()) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Product added successfully',
                'data' => $product,
            ], 200);
        } else {
            return response()->json(['statusCode' => 400, 'message' => 'Product already exists', 'data' => []], 400);
        }
    }
    public function fetchProducts(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
        ]);
        if (empty($request->id)) {
            $products = Product::where('org_id', $request->org_id)->get();
        } else {
            $products = Product::where('org_id', $request->org_id)->where('id', $request->id)->first();
        }

        if ($products) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Products fetched successfully',
                'data' => $products,
            ], 200);
        } else {
            return response()->json(['statusCode' => 400, 'message' => 'Products not found', 'data' => []], 400);
        }
    }
}
