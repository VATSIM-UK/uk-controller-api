<?php

namespace App\Models\Stand;

use Illuminate\Database\Eloquent\Model;

class StandAssignment extends Model
{
    const UPDATED_AT = false;

    public $incrementing = false;

    public $keyType = 'string';

    protected $fillable = [
        'callsign',
        'stand_id',
    ];
}
