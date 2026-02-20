<?php

namespace App\Observers;

use App\Models\Nurse;
use Illuminate\Support\Facades\Auth;

class NurseObserver
{
    public function creating(Nurse $nurse): void
    {
        if (Auth::check()) {
            $nurse->created_by = Auth::id();
            $nurse->updated_by = Auth::id();
        }
    }

    public function updating(Nurse $nurse): void
    {
        if ($nurse->isDirty('created_by')) {
            $nurse->created_by = $nurse->getOriginal('created_by');
        }
        if (Auth::check()) {
            $nurse->updated_by = Auth::id();
        }
    }
}
