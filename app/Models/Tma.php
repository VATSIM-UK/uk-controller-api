<?php

namespace App\Models;

use App\Helpers\MinStack\MinStackDataProviderInterface;
use App\Models\Airfield\Airfield;
use App\Models\MinStack\MslTma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tma extends Model implements MinStackDataProviderInterface
{
    public $timestamps = true;

    protected $table = 'tma';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'transition_altitude',
        'standard_high',
        'msl_airfield_id',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasOne
     */
    public function mslAirfield() : HasOne
    {
        return $this->hasOne(Airfield::class, 'id', 'msl_airfield_id');
    }

    /**
     * @return HasOne
     */
    public function msl() : HasOne
    {
        return $this->hasOne(MslTma::class);
    }

    /**
     * The facility against which the MSL should be calculated
     *
     * @return string
     */
    public function calculationFacility(): string
    {
        return $this->mslAirfield->code;
    }

    /**
     * The transition altitude for the facility in question
     *
     * @return int
     */
    public function transitionAltitude(): int
    {
        return $this->transition_altitude;
    }

    /**
     * True if the facility considers standard pressure (1013) to be
     * high
     *
     * @return bool
     */
    public function standardPressureHigh(): bool
    {
        return $this->standard_high;
    }
}
