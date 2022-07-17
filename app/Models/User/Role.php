<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'key',
        'description',
    ];

    protected $casts = [
        'key' => RoleKeys::class,
    ];

    public static function fromKey(RoleKeys $key): Role
    {
        return Role::where('key', $key)->firstOrFail();
    }

    public static function idFromKey(RoleKeys $key): int
    {
        return static::fromKey($key)->id;
    }

    public function isOneOf(array $roles)
    {
        return in_array($this->key, $roles);
    }
}
