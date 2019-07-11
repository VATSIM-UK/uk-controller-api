<?php

namespace App\Models;

use App\Helpers\MinStack\MinStackDataProviderInterface;
use App\Models\MinStack\MslAirfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @return HasMany
     */
    public function sids() : HasMany
    {
        return $this->hasMany(Sid::class);
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

    /**
     * @return array
     */
    public function getMslCalculationAttribute(): ?array
    {
        if (!isset($this->attributes['msl_calculation'])) {
            return null;
        }

        return json_decode($this->attributes['msl_calculation'], true);
    }
}
