<?php

namespace App\Models;

use App\Observers\BuyOrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([BuyOrderObserver::class])]
class BuyOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "clinic_id",
        "supplier_id",
        "tax",
        "subtotal",
        "total",
        "status",
        "created_by",
        "updated_by"
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function buyOrderDetails(): HasMany
    {
        return $this->hasMany(BuyOrderDetail::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
