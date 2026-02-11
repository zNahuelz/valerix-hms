<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model
{
    protected $fillable = [
        'name',
        'ruc',
        'address',
        'phone',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function buyOrders(): HasMany
    {
        return $this->hasMany(BuyOrder::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function nurses(): HasMany
    {
        return $this->hasMany(Nurse::class);
    }

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'clinic_medicines');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }
}
