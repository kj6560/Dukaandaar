<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\OauthAccessTokens;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $device_id = $request->device_id;
        $user = User::where('email', $request->email)->first();
        if (!empty($user->id) && $user->is_active == 1 && Hash::check($request->password, $user->password)) {
            if($this->checkSubscription($user->id) == false){
                return response()->json([
                    'statusCode' => 202,
                    'message' => 'You don\'t have an active subscription. Plz contact admin',
                    'data' => []
                ], 200);
            }
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            $user->device_id = $device_id;
            $user->save();
            return response()->json([
                'statusCode' => 200,
                'message' => 'Login successfully',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ], 200);
        }else{
            if(!empty($user->is_active) && $user->is_active == 0){
                return response()->json([
                    'statusCode' => 202,
                    'message' => 'User is not active.Please contact admin',
                    'data' => []
                ], 200);
            }
            if(empty($user->id)){
                return response()->json([
                    'statusCode' => 202,
                    'message' => 'User not found',
                    'data' => []
                ], 200);
            }
            if(!empty($user->is_active) && !Hash::check($request->password, $user->password)){
                return response()->json([
                    'statusCode' => 202,
                    'message' => 'Password is incorrect',
                    'data' => []
                ], 200);
            }

        }

        return response()->json([
            'statusCode' => 202,
            'message' => 'Invalid credentials',
            'data' => []
        ], 200);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'number' => 'required|string|max:255|unique:users',
            'org_id' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'org_id' => $request->org_id,
            'password' => Hash::make($request->password),
        ]);

        if (!empty($user->id)) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } else {
            return response()->json([
                'statusCode' => 500,
                'message' => 'User not created',
            ]);
        }
    }
    public function logout(Request $request)
    {
        $user = OauthAccessTokens::where('user_id', $request->user_id)->first();
        $u = User::where('id',$request->user_id)->first();
        $u->device_id = "";
        $u->save();
        if (!empty($user->user_id)) {
            $user->delete();
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['error' => true], 200);
        }
    }
}
