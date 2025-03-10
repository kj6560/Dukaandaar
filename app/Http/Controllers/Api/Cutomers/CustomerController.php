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
            'org_id' => 'required|integer',
            'customer_name' => 'required|string',
            'customer_phone_number' => 'required|string',
            'customer_address' => 'required|string',
            'customer_active' => 'required|int',
            'customer_image' => 'required|image'
        ]);

        // Check if updating an existing customer
        $customer = !empty($request->customer_id) ?
            Customer::find($request->customer_id) :
            new Customer();

        if (!$customer) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Customer not found',
            ], 404);
        }

        // Assign values
        $customer->customer_name = $request->customer_name;
        $customer->customer_phone_number = $request->customer_phone_number;
        $customer->customer_address = $request->customer_address;
        $customer->customer_active = $request->customer_active;
        $customer->org_id = $request->org_id;

        // Handle image upload
        if ($request->hasFile('customer_image')) {
            $file = $request->file('customer_image');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Unique filename
            $filePath = $file->storeAs('public/customer_images', $fileName); // Store in storage/app/public

            $customer->customer_pic = str_replace('public/', 'storage/', $filePath); // Fix path for serving
        }

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
            ], 400);
        }
    }
}
