<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Observers\AppointmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([AppointmentObserver::class])]
class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'time',
        'status',
        'is_remote',
        'duration',
        'appointment_type_id',
        'rescheduling_date',
        'rescheduling_time',
        'clinic_id',
        'treatment_id',
        'patient_id',
        'doctor_id',
        'nurse_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => AppointmentStatus::class,
    ];

    public function appointmentType(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(Nurse::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
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
