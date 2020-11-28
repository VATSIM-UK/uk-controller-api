<?php

namespace App\Models\Stand;

use Illuminate\Database\Eloquent\Model;

class StandReservation extends Model
{
    protected $fillable = [
        'stand_id',
        'callsign',
        'start',
        'end',
    ];

    protected $dates = [
        'start',
        'end',
    ];

    protected $casts = [
        'stand_id' => 'integer',
    ];
}
