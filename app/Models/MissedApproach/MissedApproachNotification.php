<?php

namespace App\Models\MissedApproach;

use Illuminate\Database\Eloquent\Model;

class MissedApproachNotification extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'callsign',
        'user_id',
    ];

    protected $dates = [
        'created_at',
    ];
}
