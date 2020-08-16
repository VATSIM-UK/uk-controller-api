<?php

namespace App\Models\Stand;

use Illuminate\Database\Eloquent\Model;

class StandAssignment extends Model
{
    const UPDATED_AT = null;

    public $incrementing = false;

    public $keyType = 'string';

    public $primaryKey = 'callsign';

    protected $fillable = [
        'callsign',
        'stand_id',
    ];
}
