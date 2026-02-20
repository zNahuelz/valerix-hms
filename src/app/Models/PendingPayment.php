<?php

namespace App\Models;

use App\Observers\PendingPaymentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([PendingPaymentObserver::class])]
class PendingPayment extends Model
{
    protected $fillable = [
        'appointment_id',
        'notes',
        'subtotal',
        'tax',
        'total',
        'created_by',
        'updated_by',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
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
