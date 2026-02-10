<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "name",
        "key",
        "description"
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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
