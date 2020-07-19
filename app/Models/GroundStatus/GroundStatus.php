<?php

namespace App\Models\GroundStatus;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroundStatus extends Model
{
    protected $fillable = [
        'display_string',
        'description',
    ];

    public function networkAircraft(): BelongsToMany
    {
        return $this->belongsToMany(
            NetworkAircraft::class,
            'ground_status_network_aircraft',
            'ground_status_id',
            'callsign',
        );
    }
}
