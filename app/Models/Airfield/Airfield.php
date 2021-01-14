<?php

namespace App\Models\Airfield;

use App\Helpers\MinStack\MinStackDataProviderInterface;
use App\Models\Aircraft\SpeedGroup;
use App\Models\Controller\ControllerPosition;
use App\Models\MinStack\MslAirfield;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Location\Coordinate;

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
        'wake_category_scheme_id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'standard_high',
        'msl_calculation',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'groundspeed' => 'integer',
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

    public function controllers() : BelongsToMany
    {
        return $this->belongsToMany(
            ControllerPosition::class,
            'top_downs',
            'airfield_id',
            'controller_position_id'
        )
            ->withPivot('order');
    }

    public function prenotePairings() : BelongsToMany
    {
        return $this->belongsToMany(
            Airfield::class,
            'airfield_pairing_prenotes',
            'origin_airfield_id',
            'destination_airfield_id'
        )->withPivot('prenote_id');
    }

    public function getCoordinateAttribute(): Coordinate
    {
        return new Coordinate($this->latitude, $this->longitude);
    }

    public function speedGroups(): HasMany
    {
        return $this->hasMany(SpeedGroup::class);
    }
}
