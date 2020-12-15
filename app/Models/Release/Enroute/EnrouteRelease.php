<?php

namespace App\Models\Release\Enroute;

use Illuminate\Database\Eloquent\Model;

class EnrouteRelease extends Model
{
    const UPDATED_AT = null;

    const CREATED_AT = 'released_at';

    protected $fillable = [
        'callsign',
        'enroute_release_type_id',
        'initiating_controller',
        'target_controller',
        'release_point',
        'user_id',
    ];
}
