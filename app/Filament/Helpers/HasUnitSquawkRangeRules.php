<?php

namespace App\Filament\Helpers;

use App\Filament\Resources\TranslatesStrings;
use Filament\Forms\Components\Select;

trait HasUnitSquawkRangeRules
{
    use TranslatesStrings;

    private static function unitSquawkRangeRules(): array
    {
        return [
            Select::make('flight_rules')
                ->label(self::translateFormPath('rule_flight_rules.label'))
                ->helperText(self::translateFormPath('rule_flight_rules.helper'))
                ->options([
                    SquawkRangeFlightRules::Ifr->value => 'IFR',
                    SquawkRangeFlightRules::Vfr->value => 'VFR',
                ]),
            Select::make('unit_type')
                ->label(self::translateFormPath('rule_unit_type.label'))
                ->helperText(self::translateFormPath('rule_unit_type.helper'))
                ->options([
                    SquawkRangeUnitTypes::Delivery->value => 'Delivery',
                    SquawkRangeUnitTypes::Ground->value => 'Ground',
                    SquawkRangeUnitTypes::Tower->value => 'Tower',
                    SquawkRangeUnitTypes::Approach->value => 'Approach',
                    SquawkRangeUnitTypes::Enroute->value => 'Enroute',
                ]),
            Select::make('service')
                ->label(self::translateFormPath('rule_service.label'))
                ->helperText(self::translateFormPath('rule_service.helper'))
                ->options([
                    SquawkRangeService::Basic->value => 'Basic',
                    SquawkRangeService::Traffic->value => 'Traffic',
                    SquawkRangeService::Deconfliction->value => 'Deconfliction',
                    SquawkRangeService::Procedural->value => 'Procedural',
                ]),
        ];
    }
}
