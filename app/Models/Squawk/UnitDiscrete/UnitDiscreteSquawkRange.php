<?php

namespace App\Models\Squawk\UnitDiscrete;

use App\Caster\UnitDiscreteSquawkRangeRuleCaster;
use App\Models\Squawk\AbstractSquawkRange;
use Illuminate\Support\Collection;

class UnitDiscreteSquawkRange extends AbstractSquawkRange
{
    protected $fillable = [
        'unit',
        'first',
        'last',
        'rules',
    ];

    protected $casts = [
        'rules' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function first(): string
    {
        return $this->attributes['first'];
    }

    public function last(): string
    {
        return $this->attributes['last'];
    }

    public function ruleCollection(): Collection
    {
        return tap(
            collect(),
            function (Collection $rules) {
                if (!$this->rules) {
                    return;
                }

                foreach ($this->rules as $rule) {
                    $rules->add(
                        (new UnitDiscreteSquawkRangeRuleCaster())->get(
                            $rule,
                        )
                    );
                }
            }
        );
    }
}
