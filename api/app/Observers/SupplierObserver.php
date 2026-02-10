<?php

namespace App\Observers;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class SupplierObserver
{
    public function creating(Supplier $supplier): void
    {
        if (Auth::check()) {
            $supplier->created_by = Auth::id();
            $supplier->updated_by = Auth::id();
        }
    }

    public function updating(Supplier $supplier): void
    {
        if ($supplier->isDirty('created_by')) {
            $supplier->created_by = $supplier->getOriginal('created_by');
        }
        if (Auth::check()) {
            $supplier->updated_by = Auth::id();
        }
    }
}
