<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SubsFeature;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function showPurchasePage()
    {
        $features = SubsFeature::with('details')->get();
        return view('subscription.purchase', compact('features'));
    }
}
