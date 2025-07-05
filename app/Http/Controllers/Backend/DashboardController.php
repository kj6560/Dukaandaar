<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Orders;
use App\Models\Organization;
use App\Models\Product;
use App\Models\SubsFeature;
use App\Models\User;
use App\Models\UserFeaturePurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $users = User::where('org_id', $org->id)->where('role', '!=', 1)->count();
        $organizations = Organization::where('is_active', 1)->count();
        $userId = auth()->user()->id;
        $activeSubscription = UserFeaturePurchase::where('org_id', auth()->user()->org_id)
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
            'total_users' => $users,
            'total_organizations' => $organizations,
            'showSubscriptionFeatures' => !empty($activeSubscription->id) ? 1 : 0,
        ]);
    }
    public function listOrganizations(Request $request)
    {
        if ($request->ajax()) {
            $data = Organization::select(['id', 'org_name', 'org_email', 'org_number', 'org_address', 'is_active', 'created_at']);
            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    return $row->is_active ? 'Active' : 'Inactive';
                })
                ->rawColumns(['status'])
                ->make(true);
        }
        return view('backend.dashboard.listOrganizations');
    }
    public function toggleStatus(Request $request, $id)
    {
        $user_feature_purchases = UserFeaturePurchase::where('org_id', $id)->first();
        if (empty($user_feature_purchases)) {
            return response()->json(['success' => false]);
        }
        $org = Organization::findOrFail($id);
        $org->is_active = $request->is_active;
        if ($org->save()) {
            $user_feature_purchases->expired = $request->is_active;
            if ($user_feature_purchases->save()) {
                return response()->json(['success' => true]);
            }

        }
        return response()->json(['success' => false]);

    }
    public function org_detail(Request $request, $id)
    {
        $organization = Organization::where('id', $id)->first();
        $user_feature_purchases = UserFeaturePurchase::where('org_id', $id)->first();
        $subs_features = SubsFeature::all();
        return view('backend.dashboard.org_detail', ['organization' => $organization, 'purchase' => $user_feature_purchases, 'features' => $subs_features]);
    }
    public function saveOrg(Request $request)
    {
        $data = $request->all();
        if (!empty($data['id'])) {
            $org = Organization::where('id', $data['id'])->first();
        } else {
            $org = new Organization();
        }
        $org->org_name = $data['org_name'];
        $org->org_email = $data['org_email'];
        $org->org_number = $data['org_number'];
        $org->org_address = $data['org_address'];
        $org->is_active = $data['is_active'];
        if ($org->save()) {
            $user = User::where('email', $data['org_email'])->first();
            if (empty($user)) {
                $user->is_active = $data['is_active'];
                $user->save();
            }
            return redirect()->back()->with('success', "success");
        }
    }
    public function saveUserFeaturePurchase(Request $request)
    {
        $data = $request->all();
        if (!empty($data['id'])) {
            $userFeaturePurchase = UserFeaturePurchase::where('id', $data['id'])->first();
        } else {
            $userFeaturePurchase = new UserFeaturePurchase();
            $userFeaturePurchase->org_id = $data['org_id'];
        }
        $userFeaturePurchase->feature_id = $data['feature_id'];
        $userFeaturePurchase->transaction_id = $data['transaction_id'];
        $userFeaturePurchase->purchased_at = $data['purchased_at'];
        $userFeaturePurchase->expires_at = $data['expires_at'];
        $userFeaturePurchase->expired = $data['expired'];
        if ($userFeaturePurchase->save()) {
            return redirect()->back()->with('success', "success");
        } else {
            return redirect()->back()->with('error', 'some error');
        }
    }
    public function uploadApk(Request $request){
        $request->validate([
            'apk_file' => 'required|file|mimes:apk,zip,application/octet-stream|max:51200', // max 50MB
        ]);

        $apkPath = 'public/uploads/fizsell.apk';
        
        if (Storage::exists($apkPath)) {
            Storage::delete($apkPath);
        }

        $file = $request->file('apk_file');

        // Store in storage/app/public/uploads/
        $path = $file->storeAs('public/uploads', 'fizsell.apk');

        return response()->json([
            'status' => 'success',
            'path' => Storage::url('uploads/fizsell.apk'),
        ]);
    }
}
