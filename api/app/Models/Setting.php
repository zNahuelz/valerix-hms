<?php

namespace App\Models;

use App\Observers\SettingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([SettingObserver::class])]
class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'value_type',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $appends = ['key'];

    protected $hidden = ['_key'];

    protected function key(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['_key'],
            set: fn($value) => ['_key' => $value],
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
