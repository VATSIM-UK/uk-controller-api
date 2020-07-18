<?php

namespace App\Models\FlightInformationRegion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightInformationRegionBoundary extends Model
{
    protected $fillable = [
        'flight_information_region_id',
        'start_latitude',
        'start_longitude',
        'finish_latitude',
        'finish_longitude',
        'description',
    ];

    public function flightInformationRegion(): BelongsTo
    {
        return $this->belongsTo(FlightInformationRegion::class);
    }
}
