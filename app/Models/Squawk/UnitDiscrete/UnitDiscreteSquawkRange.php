<?php

namespace App\Models\Squawk\UnitDiscrete;

use App\Caster\UnitDiscreteSquawkRangeRuleCaster;
use App\Models\Squawk\AbstractSquawkRange;

class UnitDiscreteSquawkRange extends AbstractSquawkRange
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'unit',
        'first',
        'last',
        'rule',
    ];

    protected $casts = [
        'rule' => UnitDiscreteSquawkRangeRuleCaster::class,
    ];

    public function first(): string
    {
        return $this->attributes['first'];
    }

    public function last(): string
    {
        return $this->attributes['last'];
    }

    public function hasRule(): bool
    {
        return !is_null($this->attributes['rule']);
    }
}
