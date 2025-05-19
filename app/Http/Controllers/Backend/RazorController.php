<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        dd($order);
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
}
