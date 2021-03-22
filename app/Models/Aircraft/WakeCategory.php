<?php

namespace App\Models\Aircraft;

use Illuminate\Database\Eloquent\Builder;
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'subsequent_departure_intervals' => $this->departureIntervals->sortBy('relative_weighting')->map(
                function (WakeCategory $subsequent) {
                    return [
                        'id' => $subsequent->id,
                        'interval' => $subsequent->pivot->interval,
                        'intermediate' => $subsequent->pivot->intermediate,
                    ];
                }
            )->values()->toArray(),
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
        )->withPivot('intermediate', 'interval');
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(WakeCategoryScheme::class, 'wake_category_scheme_id');
    }
}
