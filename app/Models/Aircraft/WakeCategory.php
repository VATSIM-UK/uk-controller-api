<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WakeCategory extends Model
{
    protected $fillable = [
        'wake_category_scheme_id',
        'code',
        'description',
        'relative_weighting',
        'created_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'relative_weighting' => 'integer',
    ];

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'relative_weighting' => $this->relative_weighting,
            'subsequent_departure_intervals' => $this->departureIntervals
                ->sortBy('relative_weighting')
                ->map(
                    function (WakeCategory $subsequent) {
                        return [
                            'id' => $subsequent->id,
                            'interval' => $subsequent->pivot->interval,
                            'interval_unit' => $subsequent->pivot->measurementUnit->unit,
                            'intermediate' => (bool)$subsequent->pivot->intermediate,
                        ];
                    }
                )
                ->values()
                ->sortBy([
                    ['id', 'asc'],
                    ['interval', 'asc'],
                    ['intermediate', 'asc'],
                ])
                ->toArray(),
            'subsequent_arrival_intervals' => $this->arrivalIntervals
                ->sortBy('relative_weighting')
                ->map(
                    fn (WakeCategory $subsequent) => [
                        'id' => $subsequent->id,
                        'interval' => $subsequent->pivot->interval
                    ]
                )
                ->values()
                ->sortBy('id')
                ->toArray(),
        ];
    }

    public function scopeGreaterRelativeWeighting(Builder $builder, WakeCategory $wakeCategory): Builder
    {
        return $builder->where('relative_weighting', '>=', $wakeCategory->relative_weighting);
    }

    public function departureIntervals(): BelongsToMany
    {
        return $this->belongsToMany(
            WakeCategory::class,
            'departure_wake_intervals',
            'lead_wake_category_id',
            'following_wake_category_id'
        )->using(DepartureWakeInterval::class)->withPivot(['measurement_unit_id', 'interval', 'intermediate']);
    }

    public function arrivalIntervals(): BelongsToMany
    {
        return $this->belongsToMany(
            WakeCategory::class,
            'arrival_wake_intervals',
            'lead_wake_category_id',
            'following_wake_category_id'
        )->withPivot(['interval']);
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(WakeCategoryScheme::class, 'wake_category_scheme_id');
    }

    public function aircraft() : BelongsToMany
    {
        return $this->belongsToMany(
            Aircraft::class,
            'aircraft_wake_category',
        );
    }
}
