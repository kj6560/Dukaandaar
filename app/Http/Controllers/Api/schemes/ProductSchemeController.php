<?php

namespace App\Http\Controllers\Api\schemes;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductScheme;
use Illuminate\Http\Request;

class ProductSchemeController extends Controller
{
    // Get all schemes
    public function index(Request $request)
    {
        $request->validate([
            'org_id' => 'required|exists:organization,id',
        ]);
        $productSchemes = ProductScheme::where('org_id', $request->org_id)->where('is_active', 1)->with(['product' => function ($query) {
            $query->where('is_active', 1);
        }])->get();
        foreach ($productSchemes as $scheme) {
            if ($scheme->bundle_products != null && $scheme->bundle_products != "") {
                $products = [];
                $schemeProducts = json_decode($scheme->bundle_products);
                foreach ($schemeProducts as $product) {
                    $product = Product::where('id', $product->product_id)->first();
                    if(empty($product->images)){
                        $product->images = [];
                    }
                    $products[] = $product;
                }
                unset($scheme->bundle_products);
                $scheme->bundle_products = $products;
            }
        }
        return response()->json([
            'statusCode' => 200,
            'message' => 'Products fetched successfully',
            'data' => $productSchemes,
        ], 200);
    }

    // Create a scheme
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'org_id' => 'required|exists:organization,id',
            'scheme_name' => 'required|string',
            'type' => 'required|in:fixed_discount,bogs,combo',
            'value' => 'nullable|numeric',
            'duration' => 'nullable|integer',
            'bundle_products' => 'nullable|array',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        if ($request->has('bundle_products')) {
            $data['bundle_products'] = json_encode($request->bundle_products);
        }

        if (!empty($request->id)) {
            $scheme = ProductScheme::findOrFail($request->id);
            $scheme->update($data);
        } else {
            $scheme = ProductScheme::create($data);
        }
        $product = Product::where('id', $scheme->product_id)->first();

        $scheme->product = $product;
        return response()->json(['message' => 'Scheme created successfully', 'data' => $scheme], 201);
    }

    // Get a single scheme
    public function show(Request $request)
    {
        $scheme = ProductScheme::with('product')->find($request->id);
        if (!$scheme)
            return response()->json(['message' => 'Scheme not found'], 404);

        if ($scheme->bundle_products != null && $scheme->bundle_products != "") {
            $products = [];
            $schemeProducts = json_decode($scheme->bundle_products);
            foreach ($schemeProducts as $product) {
                $product = Product::where('id', $product->product_id)->first();
                $products[] = $product;
            }
            unset($scheme->bundle_products);
            $scheme->bundle_products = $products;
        }
        return response()->json([
            'statusCode' => 200,
            'message' => 'Products fetched successfully',
            'data' => $scheme,
        ], 200);
    }

    // Delete a scheme
    public function destroy(Request $request)
    {
        $scheme = ProductScheme::find($request->id);
        if (!$scheme)
            return response()->json(['message' => 'Scheme not found'], 404);

        $scheme->delete();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Scheme Deleted successfully',
            'data' => [],
        ], 200);
    }
}
