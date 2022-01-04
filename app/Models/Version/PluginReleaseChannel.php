<?php

namespace App\Models\Version;

use Illuminate\Database\Eloquent\Model;

class PluginReleaseChannel extends Model
{
    protected $fillable = [
        'name',
        'relative_stability',
    ];

    public function isStable(): bool
    {
        return $this->name === 'stable';
    }
}
