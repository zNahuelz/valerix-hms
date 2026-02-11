<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClinicMedicine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'medicine_id',
        'buy_price',
        'sell_price',
        'tax',
        'profit',
        'stock',
        'salable',
        'last_sold_by',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(Detail::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function lastSoldBy(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'last_sold_by');
    }
}
