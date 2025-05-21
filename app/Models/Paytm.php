<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paytm extends Model
{
    public $table = "paytm";
    protected $fillable = [
        "name",
        "email",
        "mobile",
        "status",
        "fee",
        "order_id",
        "transaction_id"
    ];
}
