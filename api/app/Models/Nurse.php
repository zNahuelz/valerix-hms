<?php

namespace App\Models;

use App\Observers\NurseObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([NurseObserver::class])]
class Nurse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "names",
        "paternal_surname",
        "maternal_surname",
        "dni",
        "phone",
        "address",
        "hired_at",
        "clinic_id",
        "user_id",
        "updated_by"
    ];

    protected $casts = [
        'hired_at' => 'date'
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
