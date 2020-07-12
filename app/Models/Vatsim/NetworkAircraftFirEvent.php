<?php

namespace App\Models\Vatsim;

use App\Models\FlightInformationRegion\FlightInformationRegion;
use App\Models\Hold\AssignedHold;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetworkAircraftFirEvent extends Model
{
    protected $fillable = [
        'callsign',
        'flight_information_region_id',
        'event_type',
        'metadata',
    ];

    protected $casts = [
        'flight_information_region_id' => 'integer',
        'metadata' => 'array'
    ];

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(NetworkAircraft::class, 'callsign', 'callsign');
    }

    public function flightInformationRegion(): BelongsTo
    {
        return $this->belongsTo(FlightInformationRegion::class);
    }
}
