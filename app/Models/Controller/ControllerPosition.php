<?php

namespace App\Models\Controller;

use App\Models\Airfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ControllerPosition extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'callsign',
        'frequency',
        'created_at',
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
}
