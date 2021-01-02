<?php

namespace App\Models\Departure;

use Illuminate\Database\Eloquent\Model;

class DepartureInterval extends Model
{
    protected $fillable = [
        'type_id',
        'interval',
        'expires_at',
    ];

    public $timestamps = [
        'expires_at',
    ];
}
