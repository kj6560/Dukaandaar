<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function listProducts(Request $request){
        $org_id = Auth::user()->org_id;
        $products = Product::where('org_id',$org_id)->get();
        dd($products);
    }
}
