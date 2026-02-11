<?php

namespace App\Models;

use App\Enums\UnavailabilityReason;
use App\Observers\DoctorUnavailabilityObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([DoctorUnavailabilityObserver::class])]
class DoctorUnavailability extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'doctor_id',
        'start_datetime',
        'end_datetime',
        'reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'reason' => UnavailabilityReason::class,
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
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
