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
use Illuminate\Support\Str;

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

        $product_image_path = [];

        // 👇 Use the same reliable pattern as profile upload — handle multiple files
        $files = $request->file('images') ?? $request->file('images[]');

        if ($files instanceof \Illuminate\Http\UploadedFile) {
            $files = [$files]; // convert to array if single file
        }

        if (is_array($files)) {
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = 'products/' . $fileName;

                    $file->storeAs('products', $fileName, 'public');
                    $product_image_path[] = $filePath;
                }
            }

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
        $validated = $request->validate([
            'org_id' => 'required|integer',
            'product_sku' => 'nullable|string',
            'product_id' => 'nullable|integer',
        ]);

        $orgId = $validated['org_id'];

        // Check subscription after validating org_id
        if (!$this->checkSubscription($orgId)) {
            return response()->json([
                'statusCode' => 403,
                'message' => 'You don\'t have an active subscription. Please contact admin.',
                'data' => []
            ], 403);
        }

        $query = Product::where('org_id', $orgId)
            ->where('is_active', 1)
            ->with(['latestPrice', 'latestPrice.uom', 'schemes']);

        if ($request->has('product_id')) {
            $product = $query->where('id', $request->product_id)->first();

            if (!$product) {
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Product not found with the given product_id',
                    'data' => [],
                ], 404);
            }

            return response()->json([
                'statusCode' => 200,
                'message' => 'Product fetched successfully',
                'data' => $this->formatProductResponse($product),
            ]);
        }

        if ($request->has('product_sku')) {
            $product = $query->where('sku', $request->product_sku)->first();
            if (!$product) {
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Product not found with the given product_sku',
                    'data' => [],
                ], 404);
            }

            return response()->json([
                'statusCode' => 200,
                'message' => 'Product fetched successfully',
                'data' => $this->formatProductResponse($product),
            ]);
        }

        // Fetch all products
        $products = $query->orderBy('id', 'desc')->get();

        if ($products->isEmpty()) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'No products found',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Products fetched successfully',
            'data' => $products->map(fn($product) => $this->formatProductResponse($product)),
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
