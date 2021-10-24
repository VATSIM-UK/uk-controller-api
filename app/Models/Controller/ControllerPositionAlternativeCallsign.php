<?php

namespace App\Models\Controller;

use Illuminate\Database\Eloquent\Model;

class ControllerPositionAlternativeCallsign extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'controller_position_id',
        'callsign',
    ];
}
