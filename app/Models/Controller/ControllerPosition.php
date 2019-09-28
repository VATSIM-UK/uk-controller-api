<?php

namespace App\Models\Controller;

use Illuminate\Database\Eloquent\Model;

class ControllerPosition extends Model
{
    protected $fillable = [
        'callsign',
        'frequency',
    ];
}
