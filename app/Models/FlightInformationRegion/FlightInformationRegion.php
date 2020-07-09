<?php

namespace App\Models\FlightInformationRegion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlightInformationRegion extends Model
{
    protected $fillable = [
        'identification_code',
    ];

    public function boundaries(): HasMany
    {
        return $this->hasMany(FlightInformationRegionBoundary::class);
    }
}
