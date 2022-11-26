<?php

namespace App\Filament\Helpers;

enum SquawkRangeUnitTypes: string
{
    case Delivery = 'DEL';
    case Ground = 'GND';
    case Tower = 'TWR';
    case Approach = 'APP';
    case Enroute = 'CTR';
}
