<?php

namespace App\Models\Plugin;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PluginLog extends Model
{
    use HasFactory;
    use HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'type',
        'message',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
