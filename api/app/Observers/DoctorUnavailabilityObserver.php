<?php

namespace App\Observers;

use App\Models\DoctorUnavailability;
use Illuminate\Support\Facades\Auth;

class DoctorUnavailabilityObserver
{
    public function creating(DoctorUnavailability $doctorUnavailability): void
    {
        if (Auth::check()) {
            $doctorUnavailability->created_by = Auth::id();
            $doctorUnavailability->updated_by = Auth::id();
        }
    }

    public function updating(DoctorUnavailability $doctorUnavailability): void
    {
        if ($doctorUnavailability->isDirty('created_by')) {
            $doctorUnavailability->created_by = $doctorUnavailability->getOriginal('created_by');
        }
        if (Auth::check()) {
            $doctorUnavailability->updated_by = Auth::id();
        }
    }
}
