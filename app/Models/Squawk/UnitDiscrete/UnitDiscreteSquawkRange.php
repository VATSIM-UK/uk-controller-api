<?php

namespace App\Models\Squawk\UnitDiscrete;

use App\Caster\UnitDiscreteSquawkRangeRuleCaster;
use App\Models\Squawk\AbstractSquawkRange;
use Illuminate\Contracts\Validation\Rule;

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

    public function ruleObjects(): ?Rule
    {
        return is_null($this->rule) ? null : (new UnitDiscreteSquawkRangeRuleCaster())->get(
            $this,
            'rule',
            $this->rule,
            []
        );
    }
}
