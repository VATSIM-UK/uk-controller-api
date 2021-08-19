<?php

namespace App\Models\Acars;

use Illuminate\Database\Eloquent\Model;

class AcarsMessage extends Model
{
    protected $fillable = [
        'callsign',
        'message',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
