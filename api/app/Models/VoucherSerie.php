<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherSerie extends Model
{
    protected $fillable = [
        'voucher_type_id',
        'serie',
        'next_value',
        'is_active'
    ];

    public function voucherType(): BelongsTo
    {
        return $this->belongsTo(VoucherType::class);
    }
}
