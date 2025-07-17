<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorController extends Controller
{protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function createOrder(Request $request)
    {
        $amount = $request->input('amount');  // Amount in INR
        $currency = $request->input('currency', 'INR');  // Default to INR
        $order = $this->razorpayService->createOrder($amount, $currency);
        return response()->json([
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'key' => config('razorpay.key')
        ]);
    }

    public function razor_callback(Request $request)
    {
        $data = $request->all();
        $signature = $request->header('x-razorpay-signature');
        $expectedSignature = hash_hmac('sha256', json_encode($data), config('razorpay.secret'));

        if ($signature === $expectedSignature) {
            Log::info("Payment successful:", $data);
            return response()->json(['status' => 'Payment verified']);
        }

        return response()->json(['status' => 'Payment verification failed'], 400);
    }
    public function paymentSuccess(Request $request)
    {
        $data = $request->all();
        $api = new Api(config('razorpay.key'), config('razorpay.secret'));
        $user = Auth::user();
        $org = Organization::where('id', $user->org_id)->first();
        try {
            $razorpayOrder = $api->order->fetch($request->order_id);
            if (($razorpayOrder['id'] === $data['order_id']) && doubleval($razorpayOrder['amount_paid']) === doubleval($data['amount'])) {
                $org->is_active = 1;
                $org->save();
                if (!empty($user->id)) {
                    $user->is_active = 1;
                    $user->save();
                    return redirect()->route('dashboard')->with('success', 'Payment successful and organization activated.');
                }
            } else {
                echo "Amount mismatch. Payment verification failed.";
            }
        } catch (\Exception $e) {
            print_r( $e->getMessage());
        }
    }
}
