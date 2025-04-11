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
        try {
            $request->validate([
                'org_name' => 'required|string|max:255',
                'org_email' => 'required|string|email|max:255|unique:organization',
                'org_number' => 'required',
                'org_address' => 'required',
            ]);

            // Check if org already exists
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

            // Create new organization
            $org = new Organization();
            $org->org_name = $request->org_name;
            $org->org_email = $request->org_email;
            $org->org_number = $request->org_number;
            $org->org_address = $request->org_address;
            $org->is_active = 0;

            if ($org->save()) {
                $user = new User();
                $user->name = $request->org_name . ' Admin';
                $user->email = $request->org_email;
                $user->number = $request->org_number;
                $user->password = Hash::make($request->number); 
                $user->org_id = $org->id;
                $user->is_active = 0;
                $user->save();

                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Organization created successfully. Awaiting approval.',
                    'data' => $org
                ]);
            } else {
                return response()->json([
                    'statusCode' => 400,
                    'message' => 'Organization not created',
                    'data' => $org
                ], 400);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'statusCode' => 202,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
