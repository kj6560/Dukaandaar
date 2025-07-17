<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\SubsFeature;
use App\Models\Transaction;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;

class SubscriptionController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }
    public function showPurchasePage()
    {
        $features = SubsFeature::with('details')->get();
        return view('subscription.purchase', compact('features'));
    }
    public function payment(Request $request,$id)
    {
        $user = Auth::user();
        $org_id = $user->org_id;
        $org = Organization::find($org_id);
        if (!$org) {
            return redirect()->back()->with('error', 'Organization not found.');
        }
        $user_id = $user->id;
        $org_email = $org->org_email;
        $org_number = $org->org_number;
        $org_address = $org->org_address;
        $features = SubsFeature::with('details')->where('id', $id)->first();

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $payload = [
            'receipt' => 'rcpt_' . uniqid(),
            'amount' => intval($features->price), // Amount in paise
            'currency' => 'INR'
        ];
        try {
            $api = new Api(config('razorpay.key'), config('razorpay.secret'));
            $order = $api->order->create($payload);
        } catch (\Exception $e) {
            print($e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'error' => true,
                'data' => []
            ], 400);
        }
        if (!empty($order['id'])) {
            $transaction = new Transaction();
            $transaction->transaction_orders_id = $order['id'];
            $transaction->user_id = $user->id;
            $transaction->amount = $features->price;
            $transaction->ref_id = $features->id;
            $transaction->transaction_type = 1;
            if ($transaction->save()) {
                $data = [
                    'key' => config('razorpay.key'),
                    'order_id' => $order['id'],
                    'amount' => $features->price,
                    'order_amount_paise' => $features->price * 100,
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'user_id' => 17347,
                    'contact' => '9999999999',
                ];
                return view('razorpay.payment', ['features' => $features, 'data' => $data]);
            } else {
                return response()->json([
                    'message' => 'Transaction save failed',
                    'error' => true,
                    'data' => []
                ], 500);
            }
        }
    }
}
