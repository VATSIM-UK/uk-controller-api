<?php

namespace App\Models\Airline;

use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    protected $fillable = [
        'icao_code',
        'name',
        'callsign'
    ];
}
