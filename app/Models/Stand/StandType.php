<?php

namespace App\Models\Stand;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StandType extends Model
{
    /**
     * The different types of stand on offer
     */
    private const TYPE_KEY_INTERNATIONAL = 'INTERNATIONAL';
    private const TYPE_KEY_DOMESTIC = 'DOMESTIC';
    private const TYPE_KEY_CARGO = 'CARGO';
    private const TYPE_KEY_BUSINESS_AVIATION = 'BUSINESS AVIATION';

    protected $fillable = [
        'key',
    ];

    public function scopeDomestic(Builder $builder)
    {
        return $builder->where('key', self::TYPE_KEY_DOMESTIC);
    }

    public function scopeInternational(Builder $builder)
    {
        return $builder->where('key', self::TYPE_KEY_INTERNATIONAL);
    }

    public function scopeCargo(Builder $builder)
    {
        return $builder->where('key', self::TYPE_KEY_CARGO);
    }

    public function scopeNotCargo(Builder $builder)
    {
        return $builder->where('key', '<>', self::TYPE_KEY_CARGO);
    }

    public function scopeBusinessAviation(Builder $builder)
    {
        return $builder->where('key', self::TYPE_KEY_BUSINESS_AVIATION);
    }

    public function scopeNotBusinessAviation(Builder $builder)
    {
        return $builder->where('key', '<>', self::TYPE_KEY_BUSINESS_AVIATION);
    }
}
