<?php

namespace App\Models;

use App\Observers\SupplierObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([SupplierObserver::class])]
class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'manager',
        'ruc',
        'address',
        'phone',
        'email',
        'description',
        'created_by',
        'updated_by'
    ];

    public function buyOrders(): HasMany
    {
        return $this->hasMany(BuyOrder::class);
    }

    public function clinicMedicines(): HasMany
    {
        return $this->hasMany(ClinicMedicine::class);
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
