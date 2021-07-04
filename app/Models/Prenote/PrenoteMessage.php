<?php

namespace App\Models\Prenote;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrenoteMessage extends Model
{
    use SoftDeletes;

    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'departure_airfield',
        'departure_sid',
        'destination_airfield',
        'user_id',
        'controller_position_id',
        'target_controller_position_id',
    ];

    protected $dates = [
        'created_at',
        'expires_at',
        'acknowledged_at',
    ];
}
