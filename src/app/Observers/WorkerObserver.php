<?php

namespace App\Observers;

use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class WorkerObserver
{
    public function updating(Worker $worker): void
    {
        if ($worker->isDirty('created_by')) {
            $worker->created_by = $worker->getOriginal('created_by');
        }
        if (Auth::check()) {
            $worker->updated_by = Auth::id();
        }
    }
}
