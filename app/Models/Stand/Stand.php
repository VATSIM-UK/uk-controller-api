<?php

namespace App\Models\Stand;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Location\Coordinate;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stand extends Model
{
    use HasFactory;

    const QUERY_AIRLINE_ID_COLUMN = 'airlines.id';

    protected $fillable = [
        'airfield_id',
        'identifier',
        'latitude',
        'longitude',
        'terminal_id',
        'type_id',
        'origin_slug',
        'wake_category_id',
        'max_aircraft_id',
        'assignment_priority',
        'closed_at',
        'isOpen',
    ];

    protected $casts = [
        'type_id' => 'integer',
        'latitude' => 'double',
        'longitude' => 'double',
        'assignment_priority' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'closed_at',
    ];

    public function assignment(): HasOne
    {
        return $this->hasOne(StandAssignment::class);
    }

    public function getAssignedCallsignAttribute(): ?string
    {
        return $this->assignment ? $this->assignment->callsign : null;
    }

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }

    public function scopeAirfield(Builder $query, string $airfield): Builder
    {
        return $query->whereHas('airfield', function (Builder $airfieldQuery) use ($airfield) {
            return $airfieldQuery->where('code', $airfield);
        });
    }

    public function airlines(): BelongsToMany
    {
        return $this->belongsToMany(Airline::class)
            ->withPivot('id', 'destination', 'priority', 'not_before', 'callsign_slug')->withTimestamps();
    }

    public function uniqueAirlines(): BelongsToMany
    {
        return $this->belongsToMany(Airline::class)->distinct();
    }

    public function getCoordinateAttribute()
    {
        return new Coordinate($this->latitude, $this->longitude);
    }

    public function scopeUnoccupied(Builder $builder): Builder
    {
        return $builder->whereDoesntHave('occupier')
            ->whereDoesntHave('pairedStands', function (Builder $pairedStand) {
                $pairedStand->whereHas('occupier');
            });
    }

    public function occupier(): BelongsToMany
    {
        return $this->belongsToMany(
            NetworkAircraft::class,
            'aircraft_stand',
            'stand_id',
            'callsign'
        )->withPivot('updated_at');
    }

    public function scopeUnassigned(Builder $builder): Builder
    {
        return $builder->whereDoesntHave('assignment')
            ->whereDoesntHave('pairedStands', function (Builder $pairedStand) {
                $pairedStand->whereHas('assignment');
            });
    }

    public function scopeAvailable(Builder $builder): Builder
    {
        return $this->scopeNotClosed($this->scopeNotReserved($this->scopeUnassigned($this->scopeUnoccupied($builder))));
    }

    public function scopeAirline(Builder $builder, Airline $airline): Builder
    {
        return $builder->join('airline_stand', 'stands.id', '=', 'airline_stand.stand_id')
            ->where('airline_stand.airline_id', $airline->id)
            ->where(
                function (Builder $query) {
                    // Timezones here should be local because Heathrow.
                    $now = Carbon::now()->timezone('Europe/London')->toTimeString();
                    $query->whereNull('airline_stand.not_before')
                        ->orWhere('airline_stand.not_before', '<=', $now);
                }
            );
    }

    public function scopeAirlineDestination(Builder $builder, Airline $airline, array $destinationStrings): Builder
    {
        return $this->scopeAirline($builder, $airline)->whereIn('destination', $destinationStrings);
    }

    public function scopeAirlineCallsign(Builder $builder, Airline $airline, array $slugs): Builder
    {
        return $this->scopeAirline($builder, $airline)->whereIn('callsign_slug', $slugs);
    }

    public function wakeCategory(): BelongsTo
    {
        return $this->belongsTo(WakeCategory::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(StandType::class);
    }

    public function scopeCargo(Builder $builder): Builder
    {
        return $builder->whereHas('type', function (Builder $typeQuery) {
            return $typeQuery->cargo();
        });
    }

    public function scopeNotCargo(Builder $builder): Builder
    {
        return $builder->whereHas('type', function (Builder $typeQuery) {
            return $typeQuery->notCargo();
        })->orWhereNull('type_id');
    }

    public function scopeDomestic(Builder $builder): Builder
    {
        return $builder->whereHas('type', function (Builder $typeQuery) {
            return $typeQuery->domestic();
        });
    }

    public function scopeInternational(Builder $builder): Builder
    {
        return $builder->whereHas('type', function (Builder $typeQuery) {
            return $typeQuery->international();
        });
    }

    public function pairedStands(): BelongsToMany
    {
        return $this->belongsToMany(
            Stand::class,
            'stand_pairs',
            'stand_id',
            'paired_stand_id',
        )->withPivot('id');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    public function scopeAirlineTerminal(Builder $builder, Airline $airline): Builder
    {
        return $builder->whereHas('terminal', function (Builder $terminal) use ($airline) {
            $terminal->whereHas(
                'airlines',
                function (Builder $airlineQuery) use ($airline) {
                $airlineQuery->where(self::QUERY_AIRLINE_ID_COLUMN, $airline->id);
            }
            );
        });
    }

    public function scopeOrderByWeight(Builder $builder, string $direction = 'asc'): Builder
    {
        return $builder->join('wake_categories', 'wake_categories.id', 'stands.wake_category_id')
            ->orderBy('wake_categories.relative_weighting', $direction);
    }

    public function scopeOrderByAssignmentPriority(Builder $builder, string $direction = 'asc'): Builder
    {
        return $builder->orderBy('stands.assignment_priority', $direction);
    }

    /**
     * Pick a stand that has a wake category compatible with the aircraft. If the aircraft doesn't
     * have a WTC, assume the worst.
     */
    public function scopeAppropriateWakeCategory(Builder $builder, Aircraft $aircraftType): Builder
    {
        return $builder->whereHas('wakeCategory', function (Builder $query) use ($aircraftType) {
            $query->greaterRelativeWeighting(
                $aircraftType->wakeCategories()->whereHas(
                    'scheme',
                    function (Builder $scheme) {
                    $scheme->uk();
                }
                )->first() ?? WakeCategory::whereHas(
                    'scheme',
                    function (Builder $scheme) {
                    $scheme->uk();
                }
                )->where('code', 'J')->first()
            );
        });
    }

    public function maxAircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class, 'max_aircraft_id');
    }

    public function scopeAppropriateDimensions(Builder $builder, Aircraft $aircraftType): Builder
    {
        return $builder->whereHas('maxAircraft', function (Builder $aircraftQuery) use ($aircraftType) {
            $aircraftQuery->where('wingspan', '>=', $aircraftType->wingspan)
                ->where('length', '>=', $aircraftType->length);
        })
            ->orWhereDoesntHave('maxAircraft');
    }

    public function scopeSizeAppropriate(Builder $builder, Aircraft $aircraftType): Builder
    {
        return $builder->appropriateWakeCategory($aircraftType)
            ->appropriateDimensions($aircraftType);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StandReservation::class);
    }

    public function activeReservations(): HasMany
    {
        return $this->hasMany(StandReservation::class)->active();
    }

    public function scopeNotReserved(Builder $builder): Builder
    {
        return $builder->whereDoesntHave('reservations', function (Builder $reservation) {
            $reservation->active();
        });
    }

    public function reservationsInNextHour(): HasMany
    {
        return $this->hasMany(StandReservation::class)->upcoming(Carbon::now()->addHour());
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    public function close(): Stand
    {
        $this->update(['closed_at' => Carbon::now()]);
        return $this;
    }

    public function open(): Stand
    {
        $this->update(['closed_at' => null]);
        return $this;
    }

    public function scopeNotClosed(Builder $query): Builder
    {
        return $query->whereNull('closed_at');
    }

    public function getAirfieldIdentifierAttribute(): string
    {
        return sprintf('%s - %s', $this->airfield->code, $this->identifier);
    }
}
