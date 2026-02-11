<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyOrderDetail extends Model
{
    protected $fillable = [
        'buy_order_id',
        'medicine_id',
        'amount',
        'unit_price',
    ];

    public function buyOrder(): BelongsTo
    {
        return $this->belongsTo(BuyOrder::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
