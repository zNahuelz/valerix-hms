<?php

namespace App\Models;

use App\Observers\DetailObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([DetailObserver::class])]
class Detail extends Model
{
    protected $fillable = [
        'note',
        'clinic_medicine_id',
        'amount_used',
        'created_by',
        'updated_by',
    ];

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_details');
    }

    public function clinicMedicine(): BelongsTo
    {
        return $this->belongsTo(ClinicMedicine::class);
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
