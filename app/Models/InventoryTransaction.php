<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    public $table = "inventory_transactions";
    public function user() {
        return $this->belongsTo(User::class, 'transaction_by');
    }
}
