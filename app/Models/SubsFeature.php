<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubsFeature extends Model
{
    public $table = "subs_features";
    public function details()
    {
        return $this->hasMany(SubsFeaturesDetail::class, 'subs_features_id', 'id');
    }
    public function purchases()
    {
        return $this->hasMany(UserFeaturePurchase::class, 'feature_id', 'id');
    }
}
