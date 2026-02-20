<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function voucherSeries(): HasMany
    {
        return $this->hasMany(VoucherSerie::class);
    }
}
