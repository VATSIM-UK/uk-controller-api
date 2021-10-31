<?php

namespace App\Models\Database;

use App\Models\Dependency\Dependency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DatabaseTable extends Model
{
    protected $fillable = [
        'name',
    ];

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(Dependency::class)->withTimestamps();
    }
}