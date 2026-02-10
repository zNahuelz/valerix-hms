<?php

namespace App\Observers;

use App\Models\BuyOrder;
use Illuminate\Support\Facades\Auth;

class BuyOrderObserver
{
    public function creating(BuyOrder $buyOrder): void
    {
        if (Auth::check()) {
            $buyOrder->created_by = Auth::id();
            $buyOrder->updated_by = Auth::id();
        }
    }

    public function updating(BuyOrder $buyOrder): void
    {
        if ($buyOrder->isDirty('created_by')) {
            $buyOrder->created_by = $buyOrder->getOriginal('created_by');
        }
        if (Auth::check()) {
            $buyOrder->updated_by = Auth::id();
        }
    }
}
