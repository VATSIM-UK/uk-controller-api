<?php

namespace App\Models\Controller;

use App\Helpers\Vatsim\ControllerPositionInterface;
use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ControllerPosition extends Model implements ControllerPositionInterface
{
    use HasFactory;

    public $timestamps = true;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'callsign',
        'frequency',
        'requests_departure_releases',
        'receives_departure_releases',
        'created_at',
    ];

    protected $casts = [
        'frequency' => 'float',
        'requests_departure_releases' => 'boolean',
        'receives_departure_releases' => 'boolean',
        'sends_prenotes' => 'boolean',
        'receives_prenotes' => 'boolean',
    ];

    public function topDownAirfields() : BelongsToMany
    {
        return $this->belongsToMany(
            Airfield::class,
            'top_downs',
            'controller_position_id',
            'airfield_id'
        );
    }

    public function handoffs() : BelongsToMany
    {
        return $this->belongsToMany(
            Handoff::class,
            'handoff_orders',
            'controller_position_id',
            'handoff_id'
        )
            ->orderByPivot('order')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function prenotes() : BelongsToMany
    {
        return $this->belongsToMany(
            Prenote::class,
            'prenote_orders',
            'controller_position_id',
            'prenote_id'
        )
            ->orderByPivot('order')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function alternativeCallsigns(): HasMany
    {
        return $this->hasMany(ControllerPositionAlternativeCallsign::class);
    }

    public function scopeCanRequestDepartureReleases(Builder $query): Builder
    {
        return $query->where('requests_departure_releases', true);
    }

    public function scopeCanReceiveDepartureReleases(Builder $query): Builder
    {
        return $query->where('receives_departure_releases', true);
    }

    public function scopeCanSendPrenotes(Builder $query): Builder
    {
        return $query->where('sends_prenotes', true);
    }

    public function scopeCanReceivePrenotes(Builder $query): Builder
    {
        return $query->where('receives_prenotes', true);
    }

    public function getCallsign(): string
    {
        return $this->callsign;
    }

    public function getFrequency(): float
    {
        return $this->frequency;
    }

    public function isApproach(): bool
    {
        return Str::contains($this->callsign, '_APP');
    }

    public function isEnroute(): bool
    {
        return Str::contains($this->callsign, '_CTR');
    }

    public static function fromCallsign(string $callsign): ControllerPosition
    {
        return ControllerPosition::where('callsign', $callsign)->firstOrFail();
    }

    public static function fromId(int $id): ControllerPosition
    {
        return ControllerPosition::findOrFail($id);
    }
}
