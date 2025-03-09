<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductUom;
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
            }
            $product_price->product_id = $product->id;
            $product_price->price = doubleval($request->base_price);
            $product_price->uom_id = $request->uom_id;
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

    $products = Product::join('product_price', 'product_price.product_id', '=', 'products.id')
        ->join('product_uom', 'product_uom.id', '=', 'product_price.uom_id')
        ->where('products.org_id', $request->org_id)
        ->where('products.is_active', 1)
        ->where('product_price.is_active', 1)
        ->where('product_uom.is_active', 1)
        ->select(
            'products.id as id',
            'products.org_id as org_id',
            'products.name as name',
            'products.sku as sku',
            'products.product_mrp as product_mrp',
            'products.is_active as is_active',
            'products.created_at as created_at',
            'products.updated_at as updated_at',
            // Product Price
            'product_price.id as price_id',
            'product_price.price as base_price',
            'product_price.uom_id as uom_id',
            'product_price.is_active as price_is_active',
            'product_price.created_at as price_created_at',
            'product_price.updated_at as price_updated_at',
            // UOM (Unit of Measurement)
            'product_uom.id as uom_id',
            'product_uom.title as uom_title',
            'product_uom.slug as uom_slug',
            'product_uom.is_active as uom_is_active',
            'product_uom.created_at as uom_created_at',
            'product_uom.updated_at as uom_updated_at'
        );

    if ($request->has('product_id')) {
        $products = $products->where('products.id', $request->product_id)->first();
        $responseData = $products ? $this->formatProductResponse($products) : null;
    } else {
        $products = $products->orderBy('products.id', 'desc')->get();
        $responseData = $products->map(function ($product) {
            return $this->formatProductResponse($product);
        });
    }

    if ($responseData) {
        return response()->json([
            'statusCode' => 200,
            'message' => 'Products fetched successfully',
            'data' => $responseData,
        ], 200);
    } else {
        return response()->json([
            'statusCode' => 400,
            'message' => 'Products not found',
            'data' => [],
        ], 400);
    }
}

private function formatProductResponse($product)
{
    return [
        'id' => $product->id,
        'org_id' => $product->org_id,
        'name' => $product->name,
        'sku' => $product->sku,
        'product_mrp' => $product->product_mrp,
        'is_active' => $product->is_active,
        'created_at' => $product->created_at,
        'updated_at' => $product->updated_at,
        'base_price' => $product->base_price,
        'price' => [
            'id' => $product->price_id,
            'product_id' => $product->id,
            'price' => $product->base_price,
            'uom_id' => $product->uom_id,
            'is_active' => $product->price_is_active,
            'created_at' => $product->price_created_at,
            'updated_at' => $product->price_updated_at,
        ],
        'uom' => [
            'id' => $product->uom_id,
            'title' => $product->uom_title,
            'slug' => $product->uom_slug,
            'is_active' => $product->uom_is_active,
            'created_at' => $product->uom_created_at,
            'updated_at' => $product->uom_updated_at,
        ]
    ];
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
    public function fetchProductUoms(Request $request)
    {
        $productUoms = ProductUom::all();
        if ($productUoms) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Product Uoms fetched successfully',
                'data' => $productUoms,
            ], 200);
        } else {
            return response()->json(['statusCode' => 400, 'message' => 'Product Uoms not found', 'data' => []], 400);
        }
    }
}
