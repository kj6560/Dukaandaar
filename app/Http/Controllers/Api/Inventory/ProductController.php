<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
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
        if (empty($product)) {
            $product = new Product();
        }

        $product->org_id = $request->org_id;
        $product->name = $request->name;
        $product->product_mrp = doubleval($request->product_mrp);
        $product->sku = $request->sku;
        $product->is_active = 1;
        if ($product->save()) {
            $product_price = ProductPrice::where('product_id', $product->id)->first();
            if (empty($product_price)) {
                $product_price = new ProductPrice();
                $product_price->org_id = $request->org_id;
            }
            $product_price->product_id = $product->id;
            $product_price->price = doubleval($request->base_price);
            $product_price->is_active = 1;
            if ($product_price->save()) {
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Product added successfully',
                    'data' => $product,
                ], 200);
            } else {
                return response()->json(['statusCode' => 400, 'message' => 'Product price not added', 'data' => []], 400);
            }
        } else {
            return response()->json(['statusCode' => 400, 'message' => 'Product already exists', 'data' => []], 400);
        }
    }
    public function fetchProducts(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
        ]);
        if (empty($request->product_id)) {
            $products = Product::where('org_id', $request->org_id)->get();
        } else {
            $products = Product::where('org_id', $request->org_id)->where('id', $request->product_id)->first();
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
    public function deleteProduct(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
            'sku' => 'required'
        ]);
        $product = Product::where('org_id', $request->org_id)->where('sku', $request->sku)->first();
        if ($product) {
            $product->delete();
            return response()->json([
                'statusCode' => 200,
                'message' => 'Product deleted successfully',
                'data' => $product,
            ], 200);
        } else {
            return response()->json(['statusCode' => 400, 'message' => 'Product not found', 'data' => []], 400);
        }
    }
}
