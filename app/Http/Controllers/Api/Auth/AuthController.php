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
        $user = User::where('email', $request->email)->first();
        if (Hash::check($request->password, $user->password)) {
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return response()->json([
                'statusCode' => 200,
                'message' => 'Login successfully',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
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
        if (!empty($user->user_id)) {
            $user_in = User::where('id', $request->user_id)->first();
            $user->delete();
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['error' => true], 200);
        }
    }
}
