<?php

namespace App\Models\Squawk\UnitConspicuity;

use App\Caster\UnitDiscreteSquawkRangeRuleCaster;
use App\Models\Squawk\AbstractSquawkRange;
use Illuminate\Contracts\Validation\Rule;

class UnitConspicuitySquawkCode extends AbstractSquawkRange
{
    protected $fillable = [
        'unit',
        'code',
        'rules',
    ];

    protected $casts = [
        'rules' => 'array',
    ];

    public function first(): string
    {
        return $this->attributes['first'];
    }

    public function last(): string
    {
        return $this->attributes['last'];
    }
}
