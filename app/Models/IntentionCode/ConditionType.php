<?php

namespace App\Models\IntentionCode;

enum ConditionType:string
{
    case ArrivalAirfields = 'arrival_airfields';
    case ArrivalAirfieldPattern = 'arrival_airfield_pattern';
    case ExitPoint = 'exit_point';
    case MaximumCruisingLevel = 'maximum_cruising_level';
    case CruisingLevelAbove = 'cruising_level_above';
    case RoutingVia = 'routing_via';
    case ControllerPositionStartsWith = 'controller_position_starts_with';
    case Not = 'not';
    case AnyOf = 'any_of';
    case AllOf = 'all_of';
}
