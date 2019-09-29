<?php

namespace App\Models\Controller;

use App\Models\Airfield;
use App\Models\Sid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ControllerPosition extends Model
{
    public $timestamps = true;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'callsign',
        'frequency',
        'created_at',
    ];

    protected $casts = [
        'frequency' => 'float',
    ];

    public function topDownAirfields() : BelongsToMany
    {
        return $this->belongsToMany(
            Airfield::class,
            'top_downs',
            'controller_position_id',
            'airfield_id'
        );
    }

    public function sids() : BelongsToMany
    {
        return $this->belongsToMany(
            Sid::class,
            'handoffs',
            'controller_position_id',
            'sid_id'
        );
    }
}
