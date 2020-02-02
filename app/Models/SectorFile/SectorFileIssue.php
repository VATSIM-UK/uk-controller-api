<?php

namespace App\Models\SectorFile;

use Illuminate\Database\Eloquent\Model;

class SectorFileIssue extends Model
{
    protected $fillable = [
        'number',
        'plugin',
        'api',
    ];

    protected $casts = [
        'plugin' => 'boolean',
        'api' => 'boolean',
        'number' => 'integer',
    ];
}
