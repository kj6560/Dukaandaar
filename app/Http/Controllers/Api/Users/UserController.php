<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Update the user's profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file type or size',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);
        $file = $request->file('profile_picture');

        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = 'profile_pictures/' . $fileName;

        try {
            // Delete old profile picture if it exists
            if ($user->profile_pic && Storage::exists($user->profile_pic)) {
                Storage::delete($user->profile_pic);
            }

            // Store the new profile picture
            $file->storeAs('profile_pictures', $fileName, 'public');

            // Update user profile_pic field
            $user->profile_pic = $filePath;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile picture',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function fetchUsersByOrg(Request $request)
    {
        $org_id = $request->org_id;
        if (empty($org_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Org id is required',
                'errors' => 'org_id'
            ], 400);
        }
        $orgUsers = User::where('org_id', $org_id)->where('role','!=',1)->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Fetched Successfully',
            'data' => $orgUsers ?? []
        ], status: 200);
    }
    public function createNewUser(Request $request){
            $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:6',
        'number' => 'nullable|string|max:15',
        'role' => 'required|in:2,3,4,5',
        'is_active' => 'required|in:0,1',
        'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
            'data' => $orgUsers ?? []
        ], status: 422);
    }

    $data = $request->only(['name', 'email', 'number', 'role', 'is_active']);
    $data['password'] = Hash::make($request->password);

    if ($request->hasFile('profile_pic')) {
        $filePath = $request->file('profile_pic')->store('profile_pictures', 'public');
        $data['profile_pic'] = $filePath;
    }

    $user = User::create($data);

    return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], status: 200);
    }
}
