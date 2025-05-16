<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
