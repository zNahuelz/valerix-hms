<?php

namespace App\Models;

use App\Observers\HolidayObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([HolidayObserver::class])]
class Holiday extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'date',
        'is_recurring',
        "created_by",
        "updated_by"
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
