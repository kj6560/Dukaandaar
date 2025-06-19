<?php

namespace App\Http\Controllers;

use App\Models\SubsFeature;
use App\Models\UserFeaturePurchase;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    public function checkSubscription($orgId)
    {
        $hasActiveSubscription = UserFeaturePurchase::where('org_id', $orgId)
            ->where(function ($query) {
                $query->whereNull('expires_at') // Lifetime purchase
                    ->orWhere('expires_at', '>', now()); // Active subscription
            })
            ->where('expired', 1) // Fix: Use `where()` instead of `andWhere()`
            ->exists();
        return $hasActiveSubscription;
    }
    public function getAvailableFeatures($userId)
    {
        $currentDate = now();

        $features = SubsFeature::select(
            'subs_features.id',
            'subs_features.name',
            'subs_features.description',
            'subs_features.price',
            'subs_features_details.id as detail_id',
            'subs_features_details.title',
            'subs_features_details.description as detail_description',
            'subs_features_details.price as detail_price',
            'subs_features_details.is_active',
            DB::raw('IF(user_feature_purchases.id IS NOT NULL, 1, 0) as is_purchased'),
            DB::raw('IF(user_feature_purchases.expires_at IS NOT NULL AND user_feature_purchases.expires_at > NOW(), 1, 0) as is_active')
        )
            ->leftJoin('subs_features_details', 'subs_features_details.subs_features_id', '=', 'subs_features.id')
            ->leftJoin('user_feature_purchases', function ($join) use ($userId) {
                $join->on('user_feature_purchases.feature_id', '=', 'subs_features.id')
                    ->where('user_feature_purchases.user_id', '=', $userId)
                    ->where(function ($query) {
                        $query->whereNull('user_feature_purchases.expires_at')
                            ->orWhere('user_feature_purchases.expires_at', '>', now());
                    })
                    ->where('user_feature_purchases.expired', 0);
            })
            ->where('subs_features_details.is_active', 1)
            ->orderBy('subs_features.id')
            ->get();

        return $features;
    }


}
