<?php

namespace App\Http\Controllers\Api\Cutomers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function fetchCustomers(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
        ]);
        $customers = Customer::where('org_id', $request->org_id);
        if (!empty($request->customer_id)) {
            $customers = $customers->where('id', $request->customer_id)->first();
        } else {
            $customers = $customers->get();
        }
        return response()->json([
            'statusCode' => 200,
            'message' => 'Customers fetched successfully',
            'data' => $customers,
        ], 200);
    }
    public function createCustomer(Request $request)
    {
        $request->validate([
            'org_id' => 'required',
            'customer_name' => 'required',
            'customer_phone_number' => 'required',
            'customer_address' => 'required',
            'customer_active' => 'required',
            'customer_image' => 'required'
        ]);
        if (!empty($request->customer_id)) {
            $customer = Customer::where('id', $request->customer_id)->first();
        } else {
            $customer = new Customer();
        }
        $customer->customer_name = $request->customer_name;
        $customer->customer_phone_number = $request->customer_phone_number;
        $customer->customer_address = $request->customer_address;
        $customer->customer_active = $request->customer_active;
        if(!empty($request->file('customer_image'))){
            $customer->customer_image = $request->file('customer_image')->store('public/customer_images');
        }
        $customer->org_id = $request->org_id;
        if ($customer->save()) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Customer created successfully',
                'data' => $customer,
            ], 200);
        } else {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Customer creation failed',
                'data' => $customer,
            ], 400);
        }
    }
}
