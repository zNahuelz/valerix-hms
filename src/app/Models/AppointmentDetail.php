<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentDetail extends Model
{
    protected $fillable = [
        'appointment_id',
        'detail_id',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function detail(): BelongsTo
    {
        return $this->belongsTo(Detail::class);
    }
}
