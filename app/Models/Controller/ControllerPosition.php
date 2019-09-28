<?php

namespace App\Models\Controller;

use Illuminate\Database\Eloquent\Model;

class ControllerPosition extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'callsign',
        'frequency',
        'created_at',
    ];
}
