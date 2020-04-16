<?php

namespace App\Models\Srd;

use Illuminate\Database\Eloquent\Model;

class SrdRoute extends Model
{
    protected $fillable = [
        'origin',
        'destination',
        'min_level',
        'max_level',
        'route_segment',
        'sid',
        'star',
    ];

    protected $casts = [
        'min_level' => 'integer',
        'max_level' => 'integer'
    ];
}
