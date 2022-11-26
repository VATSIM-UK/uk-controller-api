<?php

namespace App\Models\Squawk;

enum UnitSquawkRangeRuleType: string
{
    case FlightRules = 'FLIGHT_RULES';
    case Service = 'SERVICE';
    case UnitType = 'UNIT_TYPE';
}
