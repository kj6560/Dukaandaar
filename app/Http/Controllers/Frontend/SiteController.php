<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(Request $request){
        if(Auth::check()){
            return redirect("/admin/dashboard");
        }
        return view("frontend.index");
    }
}
