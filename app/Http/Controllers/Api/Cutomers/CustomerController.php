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
}
