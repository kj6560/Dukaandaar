<?php

namespace App\Http\Controllers;

use App\Models\SubsFeature;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Paytm;

class PaytmController extends Controller
{

    // display a form for payment
    public function initiate(Request $request)
    {
        $id = $request->feature_id;
        $user = Auth::user();
        $feature = SubsFeature::where("id", $id)->first();
        return view('paytm.paytm', ['feature' => $feature, 'user' => $user, 'product_id' => $feature->id]);
    }

    public function pay(Request $request)
    {
        $amount = 1500; //Amount to be paid
        $userData = [
            'name' => $request->name, // Name of user
            'mobile' => $request->mobile, //Mobile number of user
            'email' => $request->email, //Email of user
            'fee' => $amount,
            'order_id' => $request->product_id . "_" . rand(1, 1000) //Order id
        ];

        $paytmuser = \App\Models\Paytm::create($userData); // creates a new database record

        $payment = PaytmWallet::with('receive');

        $payment->prepare([
            'order' => $userData['order_id'],
            'user' => $paytmuser->id,
            'mobile_number' => $userData['mobile'],
            'email' => $userData['email'], // your user email address
            'amount' => $amount, // amount will be paid in INR.
            'callback_url' => route('status') // callback URL
        ]);
        return $payment->receive();  // initiate a new payment
    }

    public function paymentCallback()
    {
        $transaction = PaytmWallet::with('receive');
        $response = $transaction->response();

        $order_id = $transaction->getOrderId();
        $order_id_parts = explode("_", $order_id);
        $feature_id = $order_id_parts[0];
        $user_id = Auth::user()->id;
        $transaction_id = $transaction->getTransactionId();
        $now = Carbon::now();

        // Define purchase and expiry dates
        $purchased_at = $now;
        $expires_at = $now->copy()->addDays(30); // 30 days from purchase date

        if ($transaction->isSuccessful()) {
            \App\Models\Paytm::where('order_id', $order_id)->update([
                'status' => 1,
                'transaction_id' => $transaction_id
            ]);

            // Insert or update user feature purchase
            \App\Models\UserFeaturePurchase::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'feature_id' => $feature_id,
                ],
                [
                    'transaction_id' => $transaction_id,
                    'purchased_at' => $purchased_at,
                    'expires_at' => $expires_at,
                    'expired' => 0,
                ]
            );

            return redirect(route('initiate.payment'))->with('message', "Your payment is successful.");
        } else if ($transaction->isFailed()) {
            \App\Models\Paytm::where('order_id', $order_id)->update([
                'status' => 0,
                'transaction_id' => $transaction_id
            ]);

            return redirect(route('initiate.payment'))->with('message', "Your payment has failed.");
        } else if ($transaction->isOpen()) {
            \App\Models\Paytm::where('order_id', $order_id)->update([
                'status' => 2,
                'transaction_id' => $transaction_id
            ]);

            return redirect(route('initiate.payment'))->with('message', "Your payment is processing.");
        }

        $transaction->getResponseMessage();
    }

}