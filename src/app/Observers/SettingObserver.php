<?php

namespace App\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class SettingObserver
{
    public function creating(Setting $setting): void
    {
        if (Auth::check()) {
            $setting->created_by = Auth::id();
            $setting->updated_by = Auth::id();
        }
    }

    public function updating(Setting $setting): void
    {
        if ($setting->isDirty('created_by')) {
            $setting->created_by = $setting->getOriginal('created_by');
        }
        if (Auth::check()) {
            $setting->updated_by = Auth::id();
        }
    }
}
