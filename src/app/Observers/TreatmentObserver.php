<?php

namespace App\Observers;

use App\Models\Treatment;
use Illuminate\Support\Facades\Auth;

class TreatmentObserver
{
    public function creating(Treatment $treatment): void
    {
        if (Auth::check()) {
            $treatment->created_by = Auth::id();
            $treatment->updated_by = Auth::id();
        }
    }

    public function updating(Treatment $treatment): void
    {
        if ($treatment->isDirty('created_by')) {
            $treatment->created_by = $treatment->getOriginal('created_by');
        }
        if (Auth::check()) {
            $treatment->updated_by = Auth::id();
        }
    }
}
