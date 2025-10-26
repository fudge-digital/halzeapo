<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'user_id',
        'action',
        'old',
        'new',
    ];

    protected $casts = [
        'old' => 'array',
        'new' => 'array',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
