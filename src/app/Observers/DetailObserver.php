<?php

namespace App\Observers;

use App\Models\Detail;
use Illuminate\Support\Facades\Auth;

class DetailObserver
{
    public function creating(Detail $detail): void
    {
        if (Auth::check()) {
            $detail->created_by = Auth::id();
            $detail->updated_by = Auth::id();
        }
    }

    public function updating(Detail $detail): void
    {
        if ($detail->isDirty('created_by')) {
            $detail->created_by = $detail->getOriginal('created_by');
        }
        if (Auth::check()) {
            $detail->updated_by = Auth::id();
        }
    }
}
