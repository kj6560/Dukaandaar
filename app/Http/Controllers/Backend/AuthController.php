<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return view("backend.login");
    }
    public function loginRequest(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('admin/dashboard');
        }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    public function register(Request $request){
        return view("backend.register");
    }
    public function registerOrg(Request $request){

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
                return redirect()->back()->with('error',"An organization with this mobile number already exists");
            }

            $org = Organization::where('org_email', $request->org_email)->first();
            
            if ($org != null && !empty($org->id)) {
                return redirect()->back()->with('error',"An organization with this email address already exists");
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
                $user->password = Hash::make($request->password); 
                $user->org_id = $org->id;
                $user->is_active = 1;
                $user->save();
                return redirect()->back()->with('success',"Organization Created Successfully. You may login in the application once your application is approved");
            } else {
                return redirect()->back()->with('error',"Failed to create an organization with given details");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
