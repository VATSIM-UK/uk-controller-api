<?php

namespace App\Models;

use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmrArea extends Model
{
    public $table = 'smr_area';

    protected $fillable = [
        'airfield_id',
        'name',
        'source',
        'coordinates',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function airfield(): BelongsTo
    {
        return $this->belongsTo(Airfield::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where(fn (Builder $query) => $query
                ->where('start_date', '<', now())
                ->orWhereNull('start_date'))
            ->where(fn (Builder $query) => $query
                ->where('end_date', '>', now())
                ->orWhereNull('end_date'));
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('end_date', '<', now());
    }
}
