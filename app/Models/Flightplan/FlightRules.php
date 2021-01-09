<?php

namespace App\Models\Flightplan;

use Illuminate\Database\Eloquent\Model;

class FlightRules extends Model
{
    protected $fillable = [
        'euroscope_key',
        'description',
    ];
}
