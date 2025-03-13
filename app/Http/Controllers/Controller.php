<?php

namespace App\Http\Controllers;

use App\Models\UserFeaturePurchase;

abstract class Controller
{
    public function checkSubscription($userId)
    {
        $hasActiveSubscription = UserFeaturePurchase::where('user_id', $userId)
            ->where(function ($query) {
                $query->whereNull('expires_at') // Lifetime purchase
                    ->orWhere('expires_at', '>', now()); // Active subscription
            })
            ->exists();
        return $hasActiveSubscription;
    }
}
