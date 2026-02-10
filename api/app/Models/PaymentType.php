<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    protected $fillable = [
        'name',
        'action'
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
