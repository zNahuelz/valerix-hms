<?php

namespace App\Observers;

use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class WorkerObserver
{
    public function updating(Worker $worker): void
    {
        if (Auth::check()) {
            $worker->updated_by = Auth::id();
        }
    }
}
