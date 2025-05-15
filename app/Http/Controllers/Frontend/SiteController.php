<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function index(Request $request){
        if(Auth::check()){
            return redirect("/admin/dashboard");
        }
        return view("frontend.index");
    }
}
