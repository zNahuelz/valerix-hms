<?php

namespace App\Models;

use App\Observers\MedicineObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([MedicineObserver::class])]
class Medicine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "name",
        "composition",
        "description",
        "barcode",
        "presentation_id",
        "created_by",
        "updated_by"
    ];

    public function buyOrders(): BelongsToMany
    {
        return $this->belongsToMany(BuyOrder::class, 'buy_order_details');
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'clinic_medicines');
    }

    public function saleDetail(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }

    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(Treatment::class, 'treatment_medicines');
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
