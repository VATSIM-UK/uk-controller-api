<?php

namespace App\Models\Stand;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class StandReservation extends Model
{
    protected $fillable = [
        'stand_id',
        'callsign',
        'cid',
        'start',
        'end',
        'origin',
        'destination'
    ];

    protected $casts = [
        'stand_id' => 'integer',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('start', '<=', Carbon::now())
            ->where('end', '>', Carbon::now());
    }

    public function scopeUpcoming(Builder $builder, Carbon $before): Builder
    {
        return $builder->where('start', '>', Carbon::now())
            ->where('start', '<=', $before);
    }

    public function scopeStandId(Builder $builder, int $standId): Builder
    {
        return $builder->where('stand_id', $standId);
    }

    public function scopeCallsign(Builder $builder, string $callsign): Builder
    {
        return $builder->where('callsign', $callsign);
    }
}
