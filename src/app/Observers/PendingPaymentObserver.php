<?php

namespace App\Observers;

use App\Models\PendingPayment;
use Illuminate\Support\Facades\Auth;

class PendingPaymentObserver
{
    public function creating(PendingPayment $pendingPayment): void
    {
        if (Auth::check()) {
            $pendingPayment->created_by = Auth::id();
            $pendingPayment->updated_by = Auth::id();
        }
    }

    public function updating(PendingPayment $pendingPayment): void
    {
        if ($pendingPayment->isDirty('created_by')) {
            $pendingPayment->created_by = $pendingPayment->getOriginal('created_by');
        }
        if (Auth::check()) {
            $pendingPayment->updated_by = Auth::id();
        }
    }
}
