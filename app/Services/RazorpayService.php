<?php

namespace App\Services;

use Razorpay\Api\Api;
use Exception;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(config('razorpay.key'), config('razorpay.secret'));
    }

    public function createOrder($payload)
    {
        try {
            $order = $this->api->order->create($payload);
            return $order;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
