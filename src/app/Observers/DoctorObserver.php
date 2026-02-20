<?php

namespace App\Observers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;

class DoctorObserver
{
    public function updating(Doctor $doctor): void
    {
        if ($doctor->isDirty('created_by')) {
            $doctor->created_by = $doctor->getOriginal('created_by');
        }
        if (Auth::check()) {
            $doctor->updated_by = Auth::id();
        }
    }
}
