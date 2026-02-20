<?php

namespace App\Observers;

use App\Models\Medicine;
use Illuminate\Support\Facades\Auth;

class MedicineObserver
{
    public function creating(Medicine $medicine): void
    {
        if (Auth::check()) {
            $medicine->created_by = Auth::id();
            $medicine->updated_by = Auth::id();
        }
    }

    public function updating(Medicine $medicine): void
    {
        if ($medicine->isDirty('created_by')) {
            $medicine->created_by = $medicine->getOriginal('created_by');
        }
        if (Auth::check()) {
            $medicine->updated_by = Auth::id();
        }
    }
}
