<?php

namespace App\Caster;

use App\Rules\UnitDiscreteSquawkRange\FlightRules;
use App\Rules\UnitDiscreteSquawkRange\Service;
use App\Rules\UnitDiscreteSquawkRange\UnitType;
use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;

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
