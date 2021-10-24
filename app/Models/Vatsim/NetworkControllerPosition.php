<?php

namespace App\Models\Vatsim;

use Illuminate\Database\Eloquent\Model;

class NetworkControllerPosition extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'cid',
        'callsign',
        'frequency',
        'controller_position_id',
    ];
}
