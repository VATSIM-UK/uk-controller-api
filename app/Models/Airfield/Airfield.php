<?php

namespace App\Models\Airfield;

use App\Helpers\MinStack\MinStackDataProviderInterface;
use App\Models\Aircraft\SpeedGroup;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Models\Controller\HasControllerHierarchy;
use App\Models\MinStack\MslAirfield;
use App\Models\Runway\Runway;
use App\Models\Stand\Stand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Location\Coordinate;

class Airfield extends Model implements HasControllerHierarchy, MinStackDataProviderInterface
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
        'updated_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'elevation' => 'integer',
    ];

    public static function fromCode(string $code): Airfield
    {
        return static::where('code', $code)->firstOrFail();
    }

    public function runways(): HasMany
    {
        return $this->hasMany(Runway::class);
    }

    public function handoff(): BelongsTo
    {
        return $this->belongsTo(Handoff::class);
    }

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
     */
    public function calculationFacility(): string
    {
        return $this->code;
    }

    /**
     * The transition altitude for the facility in question
     */
    public function transitionAltitude(): int
    {
        return $this->transition_altitude;
    }

    /**
     * True if the facility considers standard pressure (1013) to be
     * high
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
            ->withTimestamps()
            ->withPivot('order')
            ->orderByPivot('order');
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
