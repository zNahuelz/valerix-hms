<?php

namespace App\Observers;

use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;

class HolidayObserver
{
    public function creating(Holiday $holiday): void
    {
        if (Auth::check()) {
            $holiday->created_by = Auth::id();
            $holiday->updated_by = Auth::id();
        }
    }

    public function updating(Holiday $holiday): void
    {
        if ($holiday->isDirty('created_by')) {
            $holiday->created_by = $holiday->getOriginal('created_by');
        }
        if (Auth::check()) {
            $holiday->updated_by = Auth::id();
        }
    }
}
