<?php

namespace App\Observers;

use App\Models\AppointmentType;
use Illuminate\Support\Facades\Auth;

class AppointmentTypeObserver
{
    public function creating(AppointmentType $appointmentType): void
    {
        if (Auth::check()) {
            $appointmentType->created_by = Auth::id();
            $appointmentType->updated_by = Auth::id();
        }
    }

    public function updating(AppointmentType $appointmentType): void
    {
        if ($appointmentType->isDirty('created_by')) {
            $appointmentType->created_by = $appointmentType->getOriginal('created_by');
        }
        if (Auth::check()) {
            $appointmentType->updated_by = Auth::id();
        }
    }
}
