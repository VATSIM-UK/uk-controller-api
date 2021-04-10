<?php

namespace App\Models\Plugin;

use Illuminate\Database\Eloquent\Model;

class PluginEvent extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'event'
    ];

    protected $casts = [
        'event' => 'array',
    ];
}
