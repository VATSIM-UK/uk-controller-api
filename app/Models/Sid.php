<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sid extends Model
{
    protected $timestamps = true;

    protected $fillable = [
        'airfield_id',
        'identifier',
        'initial_altitude'
    ];
}
