<?php

namespace App\Models\Airfield;

use Location\Coordinate;
use App\Models\Stand\Stand;
use App\Models\Airfield\Terminal;
use App\Models\Aircraft\SpeedGroup;
use App\Models\MinStack\MslAirfield;
use Illuminate\Database\Eloquent\Model;
use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\MinStack\MinStackDataProviderInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Airfield extends Model implements MinStackDataProviderInterface
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'airfield';

    /**
     * @var array
     */
    protected $fillable = [
        'code',
        'latitude',
        'longitude',
        'elevation',
        'transition_altitude',
        'standard_high',
        'wake_category_scheme_id',
        'handoff_id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'standard_high',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'elevation' => 'integer',
    ];

    /**
     * @return HasOne
     */
    public function msl(): HasOne
    {
        return $this->hasOne(MslAirfield::class);
    }

    public function mslCalculationAirfields(): BelongsToMany
    {
        return $this->belongsToMany(
            Airfield::class,
            'msl_calculation_airfields',
            'airfield_id',
            'msl_airfield_id',
        );
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

    public function controllers(): BelongsToMany
    {
        return $this->belongsToMany(
            ControllerPosition::class,
            'top_downs',
            'airfield_id',
            'controller_position_id'
        )
            ->withPivot('order');
    }

    public function prenotePairings(): BelongsToMany
    {
        return $this->belongsToMany(
            Airfield::class,
            'airfield_pairing_prenotes',
            'origin_airfield_id',
            'destination_airfield_id'
        )->withPivot('prenote_id', 'flight_rule_id');
    }


    public function stands(): HasMany
    {
        return $this->hasMany(
            Stand::class,
            'airfield_id',
        );
    }

    public function terminals(): HasMany
    {
        return $this->hasMany(Terminal::class);
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
