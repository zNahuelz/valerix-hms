<?php

namespace App\Models;

use App\Enums\PaymentAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    protected $fillable = [
        'name',
        'action',
    ];

    protected $casts = [
        'action' => PaymentAction::class,
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
