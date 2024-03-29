<?php

namespace App\Models\Dependency;

use App\Models\Database\DatabaseTable;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dependency extends Model
{
    protected $fillable = [
        'key',
        'action',
        'local_file',
        'per_user',
        'created_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'per_user' => 'boolean',
    ];

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function databaseTables(): BelongsToMany
    {
        return $this->belongsToMany(DatabaseTable::class)->withTimestamps();
    }
}
