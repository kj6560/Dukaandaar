<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductScheme;
use App\Models\ProductUom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function addProduct(Request $request)
    {
        try {
            $product_image_path = [];
            $validatedData = $request->validate([
                'org_id' => 'required|integer',
                'sku' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log or return the exact errors
            return response()->json([
                'statusCode' => 500,
                'message' => 'Server Error',
                'error' => $e->getMessage(), // Show actual error
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }

        $product = Product::where('org_id', $request->org_id)
            ->where('sku', $request->sku)
            ->first();

        if (empty($product)) {
            $product = new Product();
        }

        $product->org_id = $request->org_id;
        $product->name = $request->name;
        $product->product_mrp = doubleval($request->product_mrp);
        $product->sku = $request->sku;
        $product->is_active = 1;
        if (!empty($product->images)) {
            $product_image_path = explode(",", $product->images);
        }
        $images = $request->file('images') ?? $request->file('images[]');

        // Force to array
        if (!is_array($images)) {
            $images = [$images];
        }

        if ($images && count($images)) {
            \Log::info('Images found:', ['count' => count($images)]);
            foreach ($images as $image) {
                if ($image && $image->isValid()) {
                    $path = $image->store('products', 'public');
                    $product_image_path[] = $path;
                }
            }
            \Log::info('Uploaded images:', $product_image_path);
            $product->images = implode(',', $product_image_path);
        }



        if ($product->save()) {
            $product_price = ProductPrice::where('product_id', $product->id)->first();
            if (empty($product_price)) {
                $product_price = new ProductPrice();
            }

            $product_price->product_id = $product->id;
            $product_price->price = doubleval($request->base_price);
            $product_price->uom_id = $request->uom_id;
            $product_price->is_active = 1;
            $product_image_path = [];
            if ($product_price->save()) {
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Product added successfully',
                    'data' => $product,
                ], 200);
            } else {
                return response()->json([
                    'statusCode' => 400,
                    'message' => 'Product price not added',
                    'data' => []
                ], 400);
            }
        } else {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Product already exists',
                'data' => []
            ], 400);
        }
    }

    public function fetchProducts(Request $request)
    {
        if ($this->checkSubscription(Auth::user()->id) == false) {
            return response()->json([
                'statusCode' => 202,
                'message' => 'You don\'t have an active subscription. Plz contact admin',
                'data' => []
            ], 202);
        }
        $request->validate([
            'org_id' => 'required',
        ]);

        $query = Product::where('org_id', $request->org_id)
            ->where('is_active', 1)
            ->with(['latestPrice', 'latestPrice.uom', 'schemes']);

        if ($request->has('product_id')) {
            $product = $query->where('id', $request->product_id)->first();
            $responseData = $product ? $this->formatProductResponse($product) : null;
        } elseif ($request->has('product_sku')) {
            $product = $query->where('sku', $request->product_sku)->first();
            $responseData = $product ? $this->formatProductResponse($product) : null;
        } else {
            $products = $query->orderBy('id', 'desc')->get();
            $responseData = $products->map(fn($product) => $this->formatProductResponse($product));
        }

        return response()->json([
            'statusCode' => $responseData ? 200 : 400,
            'message' => $responseData ? 'Products fetched successfully' : 'Products not found',
            'data' => $responseData ?? [],
        ]);
    }


    private function formatProductResponse($product)
    {
        $product_images = !empty($product->images) ? explode(',', $product->images) : [];

        return [
            'id' => $product->id,
            'org_id' => $product->org_id,
            'name' => $product->name,
            'sku' => $product->sku,
            'product_mrp' => $product->product_mrp,
            'is_active' => $product->is_active,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'images' => $product_images,
            'base_price' => optional($product->latestPrice)->price,
            'price' => [
                'id' => optional($product->latestPrice)->id,
                'product_id' => optional($product->latestPrice)->product_id,
                'price' => optional($product->latestPrice)->price,
                'uom_id' => optional($product->latestPrice)->uom_id,
                'is_active' => optional($product->latestPrice)->is_active,
                'created_at' => optional($product->latestPrice)->created_at,
                'updated_at' => optional($product->latestPrice)->updated_at,
            ],
            'uom' => [
                'id' => optional($product->latestPrice->uom)->id,
                'title' => optional($product->latestPrice->uom)->title,
                'slug' => optional($product->latestPrice->uom)->slug,
                'is_active' => optional($product->latestPrice->uom)->is_active,
                'created_at' => optional($product->latestPrice->uom)->created_at,
                'updated_at' => optional($product->latestPrice->uom)->updated_at,
            ],
            'schemes' => $product->schemes->map(function ($scheme) {
                $bundle_products = json_decode($scheme->bundle_products, true); // Decode as associative array
    
                $bundle_products = array_map(function ($pro) {
                    $pro['product'] = Product::find($pro['product_id']); // Attach product details
                    return $pro;
                }, $bundle_products ?? []);

                return [
                    'id' => $scheme->id,
                    'scheme_name' => $scheme->scheme_name,
                    'scheme_type' => $scheme->type,
                    'scheme_value' => $scheme->value,
                    'is_active' => $scheme->is_active,
                    'created_at' => $scheme->created_at,
                    'updated_at' => $scheme->updated_at,
                    'bundle_products' => $bundle_products,
                ];
            }),

        ];
    }




    public function deleteProduct(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $product = Product::where('id', $request->id)->first();
        if ($product) {
            $product->is_active = 0;
            $product->save();
            $inventory = Inventory::where('product_id', $product->id)->first();
            if ($inventory) {
                $inventory->is_active = 0;
                $inventory->save();
            }
            $product_price = ProductPrice::where('product_id', $product->id)->first();
            if ($product_price) {
                $product_price->is_active = 0;
                $product_price->save();
            }
            $product_scheme = ProductScheme::where('product_id', $product->id)->first();
            if ($product_scheme) {
                $product_scheme->is_active = 0;
                $product_scheme->save();
            }
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
