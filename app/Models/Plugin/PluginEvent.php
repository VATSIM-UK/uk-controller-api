<?php

namespace App\Models\Plugin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PluginEvent extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'event'
    ];

    protected $casts = [
        'event' => 'array',
    ];
}
