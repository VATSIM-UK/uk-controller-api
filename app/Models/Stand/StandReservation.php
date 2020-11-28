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
        'start',
        'end',
    ];

    protected $dates = [
        'start',
        'end',
    ];

    protected $casts = [
        'stand_id' => 'integer',
    ];

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('start', '<=', Carbon::now())
            ->where('end', '>=', Carbon::now());
    }
}
