<?php

namespace App\Observers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AppointmentObserver
{
    public function creating(Appointment $appointment): void
    {
        if (Auth::check()) {
            $appointment->created_by = Auth::id();
            $appointment->updated_by = Auth::id();
        }
    }

    public function updating(Appointment $appointment): void
    {
        if ($appointment->isDirty('created_by')) {
            $appointment->created_by = $appointment->getOriginal('created_by');
        }
        if (Auth::check()) {
            $appointment->updated_by = Auth::id();
        }
    }
}
