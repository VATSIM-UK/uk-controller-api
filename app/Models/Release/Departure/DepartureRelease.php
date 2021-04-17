<?php

namespace App\Models\Release\Departure;

use Illuminate\Database\Eloquent\Model;

class DepartureRelease extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'user_id',
        'expires_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'expires_at',
    ];
}
