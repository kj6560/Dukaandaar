<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Orders;
use App\Models\Organization;
use App\Models\Product;
use App\Models\SubsFeature;
use App\Models\UserFeaturePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $org = Organization::where("id", auth()->user()->id)->first();
        $customers = Customer::where("org_id", $org->id)->count();
        $products = Product::where("org_id", $org->id)->count();
        $inventory = Inventory::where("org_id", $org->id)->count();
        $orders = Orders::where("org_id", $org->id)->count();
        $userId = auth()->user()->id;
        $activeSubscription = UserFeaturePurchase::where('user_id', $userId)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where('expired', 0)
            ->first();
        if (!empty($activeSubscription)) {
            if ($request->ajax()) {
                $flattened = [];

                $features = SubsFeature::with([
                    'details' => function ($query) {
                        $query->where('is_active', 1);
                    },
                    
                ])->get();

                foreach ($features as $feature) {
                    foreach ($feature->details as $detail) {
                        $flattened[] = [
                            
                            'title' => $detail->title,
                            'detail_description' => $detail->description,
                            'detail_price' => $detail->price
                        ];
                    }
                }

                return DataTables::of(collect($flattened))->addIndexColumn()->make(true);

            }
        }


        return view("backend.dashboard.dashboard", [
            'total_customers' => $customers,
            'total_products' => $products,
            'total_inventories' => $inventory,
            'total_orders' => $orders,
            'showSubscriptionFeatures'=> !empty($activeSubscription->id) ? 1 :0,
        ]);
    }


}
