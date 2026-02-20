<?php

namespace App\Observers;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class PatientObserver
{
    public function creating(Patient $patient): void
    {
        if (Auth::check()) {
            $patient->created_by = Auth::id();
            $patient->updated_by = Auth::id();
        }
    }

    public function updating(Patient $patient): void
    {
        if ($patient->isDirty('created_by')) {
            $patient->created_by = $patient->getOriginal('created_by');
        }
        if (Auth::check()) {
            $patient->updated_by = Auth::id();
        }
    }
}
