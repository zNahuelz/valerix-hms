<?php

namespace App\Observers;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class SaleObserver
{
    public function creating(Sale $sale): void
    {
        if (Auth::check()) {
            $sale->created_by = Auth::id();
            $sale->updated_by = Auth::id();
        }
    }

    public function updating(Sale $sale): void
    {
        if ($sale->isDirty('created_by')) {
            $sale->created_by = $sale->getOriginal('created_by');
        }
        if (Auth::check()) {
            $sale->updated_by = Auth::id();
        }
    }
}
