<?php

namespace App\Models;

use App\Helpers\MinStack\MinStackDataProviderInterface;
use App\Models\MinStack\MslAirfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Airfield extends Model implements MinStackDataProviderInterface
{
    public $timestamps = true;

    protected $table = 'airfield';

    /**
     * @var array
     */
    protected $fillable = [
        'code',
        'transition_altitude',
        'standard_high',
        'msl_calculation',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasOne
     */
    public function msl() : HasOne
    {
        return $this->hasOne(MslAirfield::class);
    }

    /**
     * The facility against which the MSL should be calculated
     *
     * @return string
     */
    public function calculationFacility(): string
    {
        return $this->code;
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
