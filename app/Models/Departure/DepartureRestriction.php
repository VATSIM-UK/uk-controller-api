<?php

namespace App\Models\Departure;

use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DepartureRestriction extends Model
{
    protected $fillable = [
        'type_id',
        'interval',
        'expires_at',
    ];

    public $dates = [
        'expires_at',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'interval' => $this->interval,
            'type' => $this->type->key,
            'expires_at' => $this->expires_at->toDateTimeString(),
            'sids' => $this->sids->mapToGroups(function (Sid $sid, $key) {
                return [$sid->airfield->code => $sid->identifier];
            })->toArray(),
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DepartureRestrictionType::class);
    }

    public function sids(): BelongsToMany
    {
        return $this->belongsToMany(Sid::class);
    }

    public function scopeActive(Builder $query)
    {
        $query->where('expires_at', '>', Carbon::now());
    }

    public function expired(): bool
    {
        return $this->expires_at <= Carbon::now();
    }

    public function expire()
    {
        $this->expires_at = Carbon::now();
        $this->save();
        return $this;
    }
}
