<?php

namespace App\Caster;

use InvalidArgumentException;
use App\Rules\UnitDiscreteSquawkRange\FlightRules;
use App\Rules\UnitDiscreteSquawkRange\UnitType;
use App\Rules\UnitDiscreteSquawkRange\Service;
use Illuminate\Contracts\Validation\Rule;

class UnitDiscreteSquawkRangeRuleCaster
{
    public function get(array $rule): Rule
    {
        if ($rule['type'] === 'UNIT_TYPE') {
            return new UnitType($rule['rule']);
        } elseif ($rule['type'] === 'FLIGHT_RULES') {
            return new FlightRules($rule['rule']);
        } elseif ($rule['type'] === 'SERVICE') {
            return new Service($rule['rule']);
        }

        throw new InvalidArgumentException('Invalid rule type');
    }
}
