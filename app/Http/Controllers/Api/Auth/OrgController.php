<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OrgController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'org_name' => 'required|string|max:255',
            'org_email' => 'required|string|email|max:255|unique:organization',
            'org_number' => 'required',
            'org_address' => 'required',

        ]);

        $org = Organization::where('org_number', $request->org_number)->first();
        if (!empty($org->id)) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Organization With this number already exists',
                'data' => $org
            ], 400);
        }
        $org = Organization::where('org_email', $request->org_email)->first();
        if (!empty($org->id)) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Organization With this email already exists',
                'data' => $org
            ], 400);
        }
        $org = new Organization();
        $org->org_name = $request->org_name;
        $org->org_email = $request->org_email;
        $org->org_number = $request->org_number;
        $org->org_address = $request->org_address;
        $org->is_active = 1;
        if ($org->save()) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Organization created successfully',
                'data' => $org
            ]);
        } else {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Organization not created',
                'data' => $org
            ], 400);
        }
    }
}
