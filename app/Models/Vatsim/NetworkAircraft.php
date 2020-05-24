<?php

namespace App\Models\Vatsim;

use App\Models\Hold\AssignedHold;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NetworkAircraft extends Model
{
    protected $fillable = [
        'callsign',
        'latitude',
        'longitude',
        'altitude',
        'groundspeed',
        'planned_aircraft',
        'planned_depairport',
        'planned_destairport',
        'planned_altitude',
        'transponder',
        'planned_flighttype',
        'planned_route',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function (NetworkAircraft $aircraft) {
            $aircraft->setUpdatedAt(Carbon::now());
        });
    }

    public function assignedHold(): HasOne
    {
        return $this->hasOne(AssignedHold::class, 'callsign', 'callsign');
    }
}
