<?php

namespace App\Models\Squawk;

use Illuminate\Database\Eloquent\Model;
use Location\Coordinate;

class SquawkReservationMeasurementPoint extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    public function getLatLongAttribute(): Coordinate
    {
        return new Coordinate($this->latitude, $this->longitude);
    }
}
